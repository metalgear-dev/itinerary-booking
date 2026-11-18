<?php


namespace Modules\Itinerary\Controllers;


use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Modules\Itinerary\Models\Inquiry;
use Modules\Itinerary\Models\InquiryForm;
use Modules\Itinerary\Models\ItineraryTour;
use Modules\Itinerary\Models\ItineraryUser;
use Modules\Itinerary\Models\WidgetFile;
use Modules\Media\Models\MediaFile;

class TourController {

    public function __construct() {

        require_once (__DIR__ . '/../../../modules/Itinerary/Helpers/Util.php');

        date_default_timezone_set("Asia/Tokyo");
    }

    public function getSpotsData($hash) {
//        try {
//            $id = Crypt::decrypt($hash);
//            $row = ItineraryTour::find($id);
//        } catch(\Exception $exception) {
//            $row = null;
//        }
        $row = ItineraryTour::find($hash);
        if ($row && $row->type != 'recommended') {
            abort(403);
        }
        $data = $row ? $row->getSpotsData() : [];
        return response()->json($data);
    }

    public function getInquiry(Request $request, $id) {
        $row = Inquiry::find($id);
        if ($row) {
            $data = $row ? $row->tour->getSpotsData() : [];
        } else {
            $data = ["no data"];
//            abort(403);
        }
        // @terminator
        $guider = User::find($row->guider_id);
        if ($guider && $guider->push_token) {
            $user_name = '';
            if ($row->user_id) {
                $user = User::find($row->user_id);
                $user_name = $user->name_or_email;
            } else {
                $form_content = json_decode($row['form_content'], true);
                $user_name = $form_content["お名前"];
            }
            $this->sendPushNotification($guider->push_token, $user_name.'相談room-'.$id.'に入ってきました。');
        }
        return response()->json($data);
    }

    // @terminator

    public function sendPushNotification($push_token, $body)
    {
        $post_data = array(
            'to'            => $push_token,
            'collapse_key'  => 'type_a',
            'notification'  => array(
                'title' => '通知',
                'body'  => $body
            ),
        );
        $ch = curl_init("https://fcm.googleapis.com/fcm/send");
        $header = array(
            'Content-Type: application/json',
            "Authorization: key=AAAASWuFf3s:APA91bHxzqQn79aK3sc8rpBLlL28RUl6sI1PWDHcS0iTExYd40b-jUyQItZYu_0sSyYojO0D4ANN32XChk6hnOVwCZtU48MMeyogUoughQHnXtrQiQRaSzxApd3pbpert1asDNH-VoMa"
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        $result =   curl_exec($ch);
        curl_close($ch);
        if ($result === FALSE) {
            //log_message("DEBUG", 'Curl failed: ' . curl_error($ch));
        } else {
            $result =  json_decode($result);
            if ($result->success === 1) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function saveSpotsData(Request $request) {
        $inquiry_id = $request->input('inquiry_id');
        $spots_data = $request->input('spots_data');
        $is_finished = $request->input('is_finished');
        $inquiry = Inquiry::find($inquiry_id);
        if ($inquiry) {
            $old_tour = $inquiry->tour;
            $new_tour_id = DB::table('itn_tours')->insertGetId(
                [
                    'prototype_id'  => $old_tour->prototype_id,
                    'customer_id'   => $inquiry->user_id,
                    'guider_id'     => $old_tour->guider_id,
                    'type'          => 'arranged',
                    'status'        => 'publish',
                    'created_at'    => date('Y-m-d H:i:s')
                ]
            );
            foreach ($spots_data as $day_index => $spots_by_day) {
                foreach ($spots_by_day as $visit_index => $spot) {

                    // change image url in case that it can include google.
                    $spot['image_url'] = $this->change_image_url($spot['image_url']);

                    $spot_id = DB::table('itn_spots')->insertGetId(
                        [
                            'title'     => $spot['title'],
                            'content'   => $spot['content'],
                            'image_url' => $spot['image_url'],
                            'address'   => $spot['address'],
                            'map_lat'   => $spot['map_lat'],
                            'map_lng'   => $spot['map_lng'],
                            'created_by'=> $old_tour->guider_id,
                            'created_at'=> date('Y-m-d H:i:s')
                        ]
                    );
                    DB::table('itn_tour_has_spots')->insert([
                        'tour_id'        => $new_tour_id,
                        'spot_id'        => $spot_id,
                        'day_order'      => $day_index,
                        'visit_order'   => $visit_index
                    ]);
                }
            }
            $inquiry_data = [
                'tour_id'   => $new_tour_id,
                'updated_at'=> date('Y-m-d H:i:s')
            ];
            if ($is_finished) $inquiry_data['status'] = 'replied';
            $result = DB::table('itn_inquiries')
                ->where('id', $inquiry_id)
                ->update($inquiry_data);
            if ($result) {
                return response()->json([
                    'status' => true,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
            ]);
        }
    }

    public function change_image_url($cur_url){
        $url_array = explode(",", $cur_url);
        $changed_url_array = [];

        foreach($url_array as $url_item){
            [$changed_url, $image_id] = $this->downloadGetImageId($url_item);
            array_push($changed_url_array, $changed_url);
        }
        return implode(",", $changed_url_array);
    }

    public function downloadGetImageId($cur_url){
        if(strpos($cur_url, 'maps.googleapis.com', 0)){
            // get folder
            $upload_path  = base_path().'/../uploads/';
            $folder = '';
            $id = Auth::id();
            if ($id) {
                $folder .= sprintf('%04d', (int)$id / 1000) . '/' . $id . '/';
            }
            date_default_timezone_set('Asia/Tokyo');
            $folder = $folder . date('Y/m/d');

            if(!file_exists($upload_path.$folder))
                mkdir($upload_path.$folder, 0777, true);
            // make new file name
            $i = 0;
            $newFileName = 'from_google_';
            do {
                $i++;
                $newFileName2 = $newFileName . $i;
                $filePath = $folder . '/' . $newFileName2 . '.' . 'jpg';
            } while (file_exists($upload_path.$filePath));
            // download from googleapi
            $image = file_get_contents($cur_url);
            $fp  = fopen($upload_path.$filePath, 'w+');
            fputs($fp, $image);
            fclose($fp);
            // save to media file database
            $fileObj = new MediaFile();
            $fileObj->file_name = $newFileName2;
            $fileObj->file_path = $filePath;
            $fileObj->file_size = filesize($upload_path.$filePath);
            $fileObj->file_type = 'image/jpeg';
            $fileObj->file_extension = 'jpg';
            $fileObj->file_width = getimagesize($upload_path.$filePath)[0];
            $fileObj->file_height = getimagesize($upload_path.$filePath)[1];
            $fileObj->save();
            $image_id = $fileObj->id;
            return [url('/uploads/'.$filePath), $image_id];
        }else{
            return [$cur_url, NULL];
        }
    }

    public function getCurrentUser() {

        $user = auth()->user();
        $respUser = [
            'id' => $user->id,
            'name' => $user->getDisplayName(),
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'parent_id' => $user->parent_id,
            'avatar_url' => get_file_url($user->image_id, 'full'),
            'is_operator' => $user->hasRole('operator') || $user->hasRole('agent')
        ];
        return response()->json($respUser);
    }

    public function getWidgetInfo($widget_id) {

        $widget = WidgetFile::query()->where('widget_id', $widget_id)->first();

        if (!$widget) {
            return response()->json([
                'success' => false,
                'message' => 'Widget not found.'
            ]);
        }
        $user = $widget->user;
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ]);
        }
        $user = new ItineraryUser($user);
        $services = [];
        foreach($user->getWidgets() as $wid) {
            $services[] = asset($wid->source_file_path);
        }
        return response()->json([
            'success' => true,
            'meta'=> [
                [
                    'name' => 'google-map-key',
                    'content' => setting_item('map_gmap_key')
                ],[
                    'name' => 'itinerary-tour',
                    'content' => $widget->tour_id
                ],[
                    'name' => 'widget-service',
                    'content' => $this->getUserService($user->service, $widget->service)
                ],[
                    'name' => 'chat-service',
                    'content' => '{"chatting":true,"voice":false,"video":false,"coBrowsing":false}'
                ]
            ],
            'widgets' => $services,
        ]);
    }

    public function getInquiryForm($widget_id) {

        $widget = WidgetFile::query()->where('widget_id', $widget_id)->first();

        if (!$widget) {
            return response()->json([
                'success' => false,
                'message' => 'Widget not found.'
            ]);
        }

        $user = $widget->user;

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ]);
        }

        $form = InquiryForm::findOrCreateByUserId($user->id);

        if ($user->hasRole('operator')) {
            $form = InquiryForm::where('user_id', $user->parent_id)->orderBy('id', 'desc')->first();
        }

        $fields = $form->fields()->get() ?? [];

        foreach ($fields as $field) {

            $field->t_name = t('itinerary::form', $field->name);
            if($field->name === "inquiry")
                $field->t_name .= "内容";
        }

        return response()->json([
            'success' => true,
            'fields' => $fields
        ]);
    }

    public function saveInquiry(Request $request) {
        $widget_id = $request->input('widget_id');
        $widget = WidgetFile::query()->where([
            'widget_id' => $widget_id,
            'status' => 1
        ])->first();
        if (!$widget) {
            abort(403);
        }
        $inquiry = new Inquiry;
        $inquiry->user_id = null;
        $inquiry->guider_id = $widget->user_id;
        $inquiry->tour_id = $widget->tour_id;
        $inquiry->form_content = json_encode($request->input('data'));
        $inquiry->reservation_date = $request->input('reservation_date');
        $inquiry->status = "applied";
        if ($inquiry->save()) {
            $email = isset($request->input('data')['メールアドレス']) ? $request->input('data')['メールアドレス'] : ($request->input('data')['email'] ?? '');
            $username = isset($request->input('data')['ネーム']) ? $request->input('data')['ネーム'] : (isset($request->input('data')['お名前']) ? $request->input('data')['お名前'] : $request->input('data')['name'] ?? 'ユーザー');
            $user_avatar = '';
            $user_id = '';
            if ($email) {
                $user = User::query()->where(['email' => $email])->first();
                if ($user) {
                    $username = $user->getDisplayName();
                    $user_avatar = $user->getAvatarUrl();
                    $user_id = $user->id;
                }
            }
            return response()->json([
                'success' => true,
                'inquiry_id' => $inquiry->id,
                'tour_id' => $inquiry->tour_id,
                'user_name' => $username,
                'user_avatar' => $user_avatar,
                'user_id' => Crypt::encrypt($user_id)
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => '問い合わせデータを保存できませんでした'
            ]);
        }
    }

    public function getReservationDate(Request $request){
        $inquiry_id = $request->input('inquiry_id');
        $inquiry = Inquiry::where([
            'id' => $inquiry_id
        ])->where('status', 'not like', 'replied')->orderBy('reservation_date', 'asc')->first();

        if (!$inquiry) {
            $result_val = "none";
        }else{
            $cur_date = $inquiry->reservation_date;
            date_default_timezone_set('Asia/Tokyo');
            $now_date = date('Y-m-d H:i:s');
            if($now_date >= $cur_date){
                $result_val = "true";
            }else{
                $result_val = "false";
            }
        }
        return response()->json([
            'reservation_date' => $result_val
        ]);
    }

    public function getSpotContent(Request $request) {
        $map_lat = $request->input('map_lat');
        $map_lng = $request->input('map_lng');
        $result = DB::table('itn_spots')
            ->where('map_lat', (string)$map_lat)
            ->where('map_lng', (string)$map_lng)
            ->orderBy('created_at', 'DESC')
            ->first();
        return response()->json([
            'success'   => true,
            'map_lat'   => $request->input('map_lat'),
            'map_lng'   => $request->input('map_lng'),
            'spot'      => $result
        ]);
    }

    public function getUserService($userService, $widgetService) {

        if (is_string($userService)) $userService = json_decode($userService);
        if (is_string($widgetService)) $widgetService = json_decode($widgetService);
        $service = new \stdClass;
        foreach(['chatting', 'voice', 'video', 'coBrowsing'] as $key) {
            $service->$key = !empty($userService->$key) && !empty($widgetService->$key);
        }
        return json_encode($service);
    }

    public function saveChattingHistory(Request $request) {
        $payload = $request->input("payload");
        $message = new Chatting();
        foreach(['inquiry_id', 'content', 'type', 'time', 'name', 'avatarUrl', 'ownerId'] as $key) {
            $message->$key = $payload[$key] ?? NULL;
        }
        if ($message->save()) {
            return json_encode(['success' => true]);
        } else {
            return json_encode(['success' => false]);
        }
    }

    public function loadChattingHistory(Request $request) {
        $inquiry_id = $request->input('inquiry_id');
        date_default_timezone_set('Asia/Tokyo');
        $date = date_create()->modify("-3 months")->format("Y-m-d H:i:s");
        $messages = Chatting::query()->where('inquiry_id',  $inquiry_id)
            ->where('time', '>=', $date)->get();
        return json_encode($messages);
    }

}

class Chatting extends Model {

    protected $table = 'itn_chattings';

    public $timestamps = false;

    public function users() {
        return $this->hasMany('App\User', 'id', 'user_id');
    }

}
