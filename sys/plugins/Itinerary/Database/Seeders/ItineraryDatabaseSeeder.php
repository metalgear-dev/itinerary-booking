<?php

namespace Plugins\Itinerary\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Itinerary\Models\Widget;
use Modules\Media\Models\MediaFile;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ItineraryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        $this->updateLanguages();

        $this->addRolesAndPermissions();

        $this->addWidgets();

        $this->addUsers();

        $this->addCurrencies();

        $this->clearBravoToursAndLocations();

        $this->createLocationsAndTours();

        $this->changeTourCatogoryNames();
    }

    // @author terminator

    function updateLanguages()
    {
        // update core_menus table and core_menu_translations table

        $en_menu_lang = DB::table('core_menus')->find(1);
        $ja_menu_lang = DB::table('core_menu_translations')->find(1);
        DB::table('core_menus')->where('name', 'Main Menu')->update([
            'items' => $ja_menu_lang->items
        ]);
        DB::table('core_menu_translations')->insert([
            'origin_id'     => 2,
            'locale'        => 'en',
            'items'         => $en_menu_lang->items,
            'create_user'   => 1
        ]);

        // update core_templates table and core_template_translations table

        $en_template_langs = DB::table('core_templates')->take(2)->get();
        $ja_template_langs = DB::table('core_template_translations')->take(2)->get();
        foreach ($en_template_langs as $en_template_lang) {
            DB::table('core_template_translations')->insert([
                'origin_id'     => $en_template_lang->id,
                'locale'        => 'en',
                'title'         => $en_template_lang->title,
                'content'       => $en_template_lang->content,
                'create_user'   => 1
            ]);
        }
        foreach ($ja_template_langs as $ja_template_lang) {
            DB::table('core_templates')->where('id', $ja_template_lang->origin_id)->update([
                'content'       => $ja_template_lang->content,
            ]);
        }
    }

    function addRolesAndPermissions() {

        Permission::findOrCreate('theme_manage');

        Permission::findOrCreate('widget_manage');

        $role_admin = Role::findOrCreate('administrator');

        $role_agent = Role::findOrCreate('agent');

        $role_operator = Role::findOrCreate('operator');

        $role_customer = Role::findOrCreate('customer');

        $role_admin->givePermissionTo(Permission::all());

        Permission::findOrCreate('inquiry_manage');

        Permission::findOrCreate('form_manage');

        Permission::findOrCreate('help_manage');

        Permission::findOrCreate('itinerary_manage');

        Permission::findOrCreate('widget_manage');

        foreach(['user_view', 'user_create', 'user_update', 'user_delete', 'widget_manage', 'inquiry_manage', 'form_manage', 'help_manage', 'itinerary_manage', 'tour_create', 'tour_update', 'tour_view', 'tour_delete'] as $perm) {
            $role_agent->givePermissionTo($perm);
        }

        foreach(['widget_manage', 'inquiry_manage', 'help_manage', 'itinerary_manage', 'tour_create', 'tour_view', 'tour_update', 'tour_delete'] as $perm) {
            $role_operator->givePermissionTo($perm);
        }
    }

    function addWidgets() {

        $chatting = Widget::findOrCreate('chatting');
        $chatting->icon_path = 'module/itinerary/widgets/icons/chatting.png';
        $chatting->source_file_path = 'module/itinerary/widgets/chatting.js';
        $chatting->save();

        $video_chatting = Widget::findOrCreate('video_chatting');
        $video_chatting->icon_path = 'module/itinerary/widgets/icons/video_chatting.png';
        $video_chatting->source_file_path = 'module/itinerary/widgets/video_chatting.js';
        $video_chatting->save();

        $voip = Widget::findOrCreate('voip');
        $voip->icon_path = 'module/itinerary/widgets/icons/voip.png';
        $voip->source_file_path = 'module/itinerary/widgets/voip.js';
        $voip->save();

    }



    function addUsers() {

        DB::table('users')->insert([
            'first_name' => '紗夏',
            'last_name' => '湊崎',
            'name' => '湊崎 紗夏',
            'email' => 'sanaminatozaki@gmail.com',
            'password' => bcrypt('111111'),
            'phone' => '821655515059',
            'status' => 'publish',
            'created_at' => date('Y-m-d H:i:s'),
            'bio' => 'Test Agent',
            'birthday' => '1996-12-29'
        ]);
        $agent = \App\User::where('email', 'sanaminatozaki@gmail.com')->first();
        $agent->assignRole('agent');

        DB::table('users')->insert([
            'first_name' => '名井',
            'last_name' => '南',
            'name' => '名井 南',
            'email' => 'minamyoui@gmail.com',
            'password' => bcrypt('111111'),
            'phone' => '821755533022',
            'status' => 'publish',
            'created_at' => date('Y-m-d H:i:s'),
            'bio' => 'Test Operator',
            'birthday' => '1997-03-24',
            'parent_id' => $agent->id
        ]);
        $operator = \App\User::where('email', 'minamyoui@gmail.com')->first();
        $operator->assignRole('operator');

    }

    function addCurrencies() {
        $table = DB::table('itn_currencies');
        $table->truncate();
        $table->insert([
            'name' => 'JPY',
            'description' => 'Japan Yen',
            'symbol' => '&#165;'
        ]);
        $table->insert([
            'name' => 'USD',
            'description' => 'United States Dollar',
            'symbol' => '&#36;'
        ]);
        $table->insert([
            'name' => 'EUR',
            'description' => 'Euro Member Countries',
            'symbol' => '&#8364;'
        ]);
    }

    // created by Metalgear
    function clearBravoToursAndLocations(){
        DB::table('bravo_tours')->truncate();
        DB::table('bravo_locations')->truncate();
    }

    function createLocationsAndTours(){
        $agent_id = \App\User::where('email', 'sanaminatozaki@gmail.com')->first()->id;
        $operator_id = \App\User::where('email', 'minamyoui@gmail.com')->first()->id;

        // for insert banner images for locations
        $image_id['agent'] = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'japan-tokyo-skytree',
                'file_path' => '0000/17/2018/09/11/japan-tokyo-skytree.jpg',
                'file_size' => '90536',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '730', 'file_height' => '487', 'created_at' => '2018-09-11 17:10:35'
            ]
        );

        $banner_image_id['agent'] = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'places-to-see-tokyo',
                'file_path' => '0000/17/2018/09/11/places-to-see-tokyo.png',
                'file_size' => '2203906',
                'file_type' => 'image/png', 'file_extension' => 'png', 'create_user' => $agent_id,
                'file_width' => '1428', 'file_height' => '747', 'created_at' => '2018-09-11 17:10:44'
            ]
        );

        $banner_image_id['operator'] = DB::table('media_files')->insertGetId(
            [
                'file_name' => '023',
                'file_path' => '0000/18/2018/09/11/023.jpg',
                'file_size' => '182595',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $operator_id,
                'file_width' => '1920', 'file_height' => '500', 'created_at' => '2018-09-11 18:21:06'
            ]
        );

        $image_id['operator'] = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'caption-3',
                'file_path' => '0000/18/2018/09/11/caption-3.jpg',
                'file_size' => '171473',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $operator_id,
                'file_width' => '1024', 'file_height' => '768', 'created_at' => '2018-09-11 18:49:39'
            ]
        );

        // now create locations
        $location_id['agent'] = DB::table('bravo_locations')->insertGetId(
            [
                'name' => 'Tokyo',
                'content' => '<p><span style="color: #2da1c4; font-family: Gotham A, Gotham B, sans-serif;"><span style="font-size: 18px;">東京は世界で最も素晴らしい都市の一つです。 テンポが速く、未来的で、豪華な神社、宮殿、寺院など、あなたを忙しくしておくための奇妙で素晴らしいアクティビティであふれています。 ヒップクラブとバー; 桜はもちろんのこと、おしゃれな人や買い物も。</span></span></p>',
                'slug' => 'tokyo',
                'image_id' => $image_id['agent'],
                'banner_image_id' => $banner_image_id['agent'],
                'map_lat' => '35.67784340964864',
                'map_lng' => '139.77037972254936',
                'map_zoom' => '11',
                'status' => 'publish',
                'create_user' => $agent_id,
                'created_at' =>  date("2018-09-11 17:17:15")
            ]
        );
        $location_id['operator'] = DB::table('bravo_locations')->insertGetId(
            [
                'name' => '北海道',
                'content' => '<h2 style="font-size: 12px; margin: 0 0 16px; padding: 0 0 4px; border: 0; clear: both; color: #698a33; font-family: Verdana, Helvetica, Arial, sans-serif;">&ldquo;另一個日本&rdquo;--北海道</h2>
                    <div class="clear" style="font-size: 11px; margin: 0; padding: 0 0 10px; border: 0; clear: both; color: #666666; font-family: Verdana, Helvetica, Arial, sans-serif; background-color: #ffffff;">
                    <p style="font-size: 12px; margin: 0 0 10px; padding: 0; border: 0; overflow: hidden; line-height: 1.5;">北海道可以說是&ldquo;另一個日本&rdquo;。日本列島四島中最北邊的這個島嶼，大約在 150 年前還是一片荒野。即使是現在，遠離城市的地方保留著一種邊遠地區特有的荒涼。在北海道，無論是城市還是鄉村，都能呼吸到清新的空氣。在這裏，找不到像梅雨季節那樣的令人窒息的舊傳統，如果您想體驗一下這種傳統的話，應該到日本其他地方去尋找，因爲到北海道來的人都是爲了逃避傳統的束縛。</p>
                    </div>
                    <h2 style="font-size: 12px; margin: 0 0 16px; padding: 0 0 4px; border: 0; clear: both; color: #698a33; font-family: Verdana, Helvetica, Arial, sans-serif;">美麗的公園城市--札幌</h2>
                    <div class="clear" style="font-size: 11px; margin: 0; padding: 0 0 10px; border: 0; clear: both; color: #666666; font-family: Verdana, Helvetica, Arial, sans-serif; background-color: #ffffff;">
                    <p style="font-size: 12px; margin: 0 0 10px; padding: 0; border: 0; overflow: hidden; line-height: 1.5;">當您來到北海道最大的城市、日本全國第 5 大城市札幌時，您一定會感到您確實獲得了解脫。習慣了日本大城市彎彎曲曲的道路的人，看到札幌筆直寬闊的街道時都會感到吃驚。和其他歷史悠久的城市不同，19 世紀 70 年代末在美國人的指導下建設的札幌街道非常便利好認，即使是初到此地的人也不會迷路。<br style="margin: 0; padding: 0;" />首先可以到大通公園去看一看。札幌市內有許多公園，這便是其中的一個。它位於札幌車站的南方，由車站往南過幾條街就到了。園內有草坪、花壇和噴水，與其說它是公園，倒不如說它更像一個散步的花園。這個花園把札幌市分成了南北兩個部分。除了大通公園以外，還有許多美麗的公園，如附近的中島公園和坐落於市區南部的真駒內公園。這些公園當然與其他地方頗有歷史的庭園難以媲美，但它們不需要門票，也沒有柵欄或"請勿踐踏草坪"之類的警告牌，更聽不到旅遊團的導遊那令人膩煩的講解和數位的羅列。<br style="margin: 0; padding: 0;" />不去公園的話，也可以租一輛自行車順著市內那條彎彎曲曲的豐平川到郊外去溜一圈。順著河堤騎車郊遊，可以使您放鬆，使您從城市生活的壓力中解脫出來。北海道的夏天和南方相比要涼快得多，濕度也低，騎幾個小時都不會感到累。<br style="margin: 0; padding: 0;" />札幌夜生活的樂趣全集中在薄野這條有幾千家酒吧、餐飲店和各種色情店的紅燈區，大約有25,000 人在這條街上從事著各種營生。</p>
                    </div>',
                'slug' => 'hokkaido',
                'map_lat' => '43.06014171376735',
                'map_lng' => '141.35374044392825',
                'map_zoom' => '6',
                'image_id' => $banner_image_id['operator'],
                'banner_image_id' => $banner_image_id['operator'],
                'status' => 'publish',
                'create_user' => $operator_id,
                'created_at' =>  '2018-09-11 17:51:20'
            ]
        );

        // insert into itn_user_has_locations
        DB::table('itn_user_has_locations')->insert([
            [
                'user_id' => $agent_id,
                'location_id' => $location_id['agent']
            ],
            [
                'operator_id' => $agent_id,
                'location_id' => $location_id['operator']
            ]
        ]);

        // now insert tours information into bravo_tours and itn_tours
        // first insert gallery images for agent
        $agentGalleryIDs[] = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'download',
                'file_path' => '0000/17/2018/09/11/download.jpg',
                'file_size' => '13634',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '265', 'file_height' => '190', 'created_at' => '2018-09-11 17:10:30'
            ]
        );

        $agentGalleryIDs[] = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'images-1',
                'file_path' => '0000/17/2018/09/11/images-1.jpg',
                'file_size' => '27796',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '275', 'file_height' => '183', 'created_at' => '2018-09-11 17:10:32'
            ]
        );

        $agentGalleryIDs[] = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'images',
                'file_path' => '0000/17/2018/09/11/images.jpg',
                'file_size' => '20075',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '275', 'file_height' => '183', 'created_at' => '2018-09-11 17:10:33'
            ]
        );

        $agentGalleryIDs[] = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'pr-p1',
                'file_path' => '0000/17/2018/09/11/pr-p1.jpg',
                'file_size' => '88227',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '755', 'file_height' => '360', 'created_at' => '2018-09-11 17:10:37'
            ]
        );

        $agentGalleryIDs[] = DB::table('media_files')->insertGetId(
            [
                'file_name' => '36840678-tokyo-japan-march-14-2014-signs-densely-line-an-alleyway-in-kabuki-cho-the-area-is-a-renown-nightlif',
                'file_path' => '0000/17/2018/09/11/36840678-tokyo-japan-march-14-2014-signs-densely-line-an-alleyway-in-kabuki-cho-the-area-is-a-renown-nightlif.jpg',
                'file_size' => '357392',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '1300', 'file_height' => '866', 'created_at' => '2018-09-11 17:10:42'
            ]
        );

        $agentGalleryStr = implode(',', $agentGalleryIDs);

        // now insert tour info for agent into bravo_tours table
        $tour_id['agent'] = DB::table('bravo_tours')->insertGetId(
            [
                'title' => '東京旅行',
                'slug' => 'tokyo-tour',
                'content' => "<p>東京は世界で最も素晴らしい都市の一つです。 テンポが速く、未来的で、豪華な神社、宮殿、寺院など、あなたを忙しくしておくための奇妙で素晴らしいアクティビティであふれています。 ヒップクラブとバー; 桜はもちろんのこと、おしゃれな人や買い物も。</p>
<p>東京はあなたがそれについて聞いたすべての誇大広告に耐えています。</p>
<p>ここで何が見つかるかわかりません。 すぐにブタのマスクと80年代のドレスを着た女性のグループに出くわし、次にロボットカフェに行きます。</p>
<p>または何世紀も前の寺院。</p>
<p>また、世界最大の都市の1つであり、1300万人以上が住んでいます。首都圏を数えると、約4,000万人です。 巨大です！</p>
<p>驚くことではありませんが、すべてを見るには時間がかかります。 探検する隅々がたくさんあるので、迷子になりやすいです。</p>",
                'image_id' => $image_id['agent'],
                'banner_image_id' => $banner_image_id['agent'],
                'category_id' => 1,
                'location_id' => $location_id['agent'],
                'short_desc' => "1日目：魚市場、上野公園、浅草
2日目：皇居、大橋の下、風変わりなカフェ
3日目：新宿、渋谷、ロボット
4日目：日帰り旅行
5日目：東京タワー、相撲、サムライ",
                'is_featured' => "0",
                'gallery' => $agentGalleryStr,
                'video' => "https://www.youtube.com/watch?v=2VulwmhHQS4",
                'price' => 2000,
                'sale_price' => 1800,
                'currency_id' => 1,
                'duration' => 120,
                'max_people' => 50,
                'min_people' => 5,
                'faqs' => '[{"title":"\u3069\u306e\u304f\u3089\u3044\u306a\u304c\u3044\u3067\u3059\u304b\u3002","content":"\u3069\u306e\u304f\u3089\u3044\u306a\u304c\u3044\u3067\u3059\u304b\u3002"},{"title":"\u3069\u306e\u304f\u3089\u3044\u304a\u3082\u3057\u308d\u3044\u3067\u3059\u304b\u3002","content":"\u3069\u306e\u304f\u3089\u3044\u304a\u3082\u3057\u308d\u3044\u3067\u3059\u304b\u3002"}]',
                'status' => "publish",
                'create_user' => $agent_id,
                'created_at' =>  '2018-09-11 17:05:38',
                'default_state' => '1'
            ]
        );

        // second insert gallery images for operator
        $operatorGalleryIDs[] = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'ph-day1-01',
                'file_path' => '0000/18/2018/09/11/ph-day1-01.jpg',
                'file_size' => '24400',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $operator_id,
                'file_width' => '220', 'file_height' => '170', 'created_at' => '2018-09-11 17:47:25'
            ]
        );

        $operatorGalleryIDs[] = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'caption',
                'file_path' => '0000/18/2018/09/11/caption.jpg',
                'file_size' => '138743',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $operator_id,
                'file_width' => '800', 'file_height' => '600', 'created_at' => '2018-09-11 18:36:20'
            ]
        );

        $operatorGalleryIDs[] = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'caption-2',
                'file_path' => '0000/18/2018/09/11/caption-2.jpg',
                'file_size' => '203974',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $operator_id,
                'file_width' => '1024', 'file_height' => '768', 'created_at' => '2018-09-11 18:46:54'
            ]
        );

        $operatorGalleryIDs[] = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'ningle-terrace',
                'file_path' => '0000/18/2018/09/11/ningle-terrace.jpg',
                'file_size' => '216731',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $operator_id,
                'file_width' => '1024', 'file_height' => '768', 'created_at' => '2018-09-11 18:58:02'
            ]
        );

        $operatorGalleryIDs[] = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'caption-4',
                'file_path' => '0000/18/2018/09/11/caption-4.jpg',
                'file_size' => '139465',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $operator_id,
                'file_width' => '1024', 'file_height' => '682', 'created_at' => '2018-09-11 19:08:09'
            ]
        );

        $operatorGalleryIDs[] = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'caption-5',
                'file_path' => '0000/18/2018/09/11/caption-5.jpg',
                'file_size' => '158546',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $operator_id,
                'file_width' => '1024', 'file_height' => '768', 'created_at' => '2018-09-11 19:10:39'
            ]
        );

        $operatorGalleryStr = implode(',', $operatorGalleryIDs);

        // now insert tour info for operator into bravo_tours table
        $tour_id['operator'] = DB::table('bravo_tours')->insertGetId(
            [
                'title' => '北海道旅行3日間',
                'slug' => 'hokkaido-tour',
                'image_id' => $image_id['operator'],
                'banner_image_id' => $banner_image_id['operator'],
                'category_id' => 1,
                'location_id' => $location_id['operator'],
                'is_featured' => "1",
                'gallery' => $operatorGalleryStr,
                'price' => 1199,
                'sale_price' => 999,
                'currency_id' => 1,
                'duration' => 72,
                'max_people' => 1,
                'min_people' => 100,
                'status' => "publish",
                'create_user' => $operator_id,
                'created_at' =>  '2018-09-11 18:37:55',
                'default_state' => '1'
            ]
        );

        // insert tour data into itn-tours
        $itn_tours['agent'] = DB::table('itn_tours')->insertGetId(
            [
                'prototype_id' => $tour_id['agent'],
                'guider_id' => $agent_id,
                'type' => 'recommended',
                'status' => 'publish',
                'created_at' => '2018-09-11 17:05:38',
                'updated_at' => '2018-09-11 17:05:38'
            ]
        );
        $itn_tours['operator'] = DB::table('itn_tours')->insertGetId(
            [
                'prototype_id' => $tour_id['operator'],
                'guider_id' => $operator_id,
                'type' => 'recommended',
                'status' => 'publish',
                'created_at' => '2018-09-11 18:37:55',
                'updated_at' => '2018-09-11 18:37:55'
            ]
        );

        // now create itinerary spots for agent and operator
        $this->createItnSpots($itn_tours, $location_id);

        // now create tour attributes and assign some of them into agent and operator tour items
        $this->changeAttributes($tour_id);
    }

    function createItnSpots($tour_id, $location_id){

        // get agent and operator id
        $agent_id = \App\User::where('email', 'sanaminatozaki@gmail.com')->first()->id;
        $operator_id = \App\User::where('email', 'minamyoui@gmail.com')->first()->id;

        // first insert images into media files table and itinerary spot information item
        // with the image is inserted into itn_spots table
        // one by one first agent's and second operator's

        // toyosu-market
        $cur_image_id = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'toyosu-market',
                'file_path' => '0000/17/2018/09/11/toyosu-market.jpg',
                'file_size' => '11795',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '300', 'file_height' => '168', 'created_at' => '2018-09-11 17:36:24'
            ]
        );

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => 'Toyosu Fish Market',
                'content' => '2018年現在、主要な魚市場は豊洲に移りました。 新しい市場は築地の2倍の規模で、世界最大の魚市場です。 入場時に訪問者のパスを必ず取得してください。

獲れたての寿司を朝食に食べ、世界最大のマグロ市場の混chaとした雰囲気に驚嘆してください。 ここでのオークションは世界の寿司供給の多くを支えており、本当に息をのむようなものです。 あなたの周りはすべて、あなたが存在することを知らなかった色と形の魚です。 私が特定できなかった魚介類を見たことがありません。

食べ物やお店を見つけることができる古い外側の市場は、同じ場所、築地にまだあります。 まだそこに行って食事や見物をすることができますが、現在の主要な市場は豊洲です。

築地魚市場：5丁目2-1中央築地、中央、03-3542-1111 入場は無料です。

豊洲魚市場：6丁目6-2豊洲、江東、03-3520-8205 ほとんどの店は午前7時まで営業していませんが、月曜日から土曜日は午前5時から午後5時まで営業しています。 入場は無料です。',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['agent'],
                'address' => '〒135-0061 東京都江東区豊洲６丁目３',
                'map_lat' => '35.64708789001671',
                'map_lng' => '139.78820661377154',
                'created_by' => $agent_id,
                'created_at' => '2018-09-11 18:01:44'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['agent'],
                'spot_id' => $spot_id,
                'day_order' => 0,
                'visit_order' => 0
            ]
        );

        // ueno-park
        $cur_image_id = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'ueno-park',
                'file_path' => '0000/17/2018/09/11/ueno-park.jpg',
                'file_size' => '12444',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '300', 'file_height' => '168', 'created_at' => '2018-09-11 17:36:24'
            ]
        );

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => 'Ueno Onshi Park',
                'content' => '上野公園は一日を過ごすのに素敵な場所です。 多くの桜の木を撮影し、ピクニックをするのに最適な場所です。 公園内のこれらのサイトをお見逃しなく：

東京国立博物館–この博物館は公園の北端にあります。 1872年に設立されたこの巨大な建物には、アジア、特に日本からの世界最大の美術品や工芸品のコレクションがあります。 台東区上野公園13-9、03-3822-1111、tnm.jp 毎日午前9時30分から午後5時まで（ほとんどの金曜日は午後8時）営業しています。 入場料は620円です。
東照宮–この17世紀の神社も公園内にあります。 彫刻が施された金色のドアと華やかな彫刻が美しい。 台東区上野公園9-88、03-3822-3455、uenotoshogu.com / en。 毎日午前9時から午後5時30分まで営業しています。 入場は無料ですが、神社に行くには500円を支払う必要があります。',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['agent'],
                'address' => '〒110-0007 東京都台東区上野公園４−・ 池之端三丁目',
                'map_lat' => '35.71411330200168',
                'map_lng' => '139.77192878723145',
                'created_by' => $agent_id,
                'created_at' => '2018-09-11 18:01:44'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['agent'],
                'spot_id' => $spot_id,
                'day_order' => 0,
                'visit_order' => 1
            ]
        );

        // National Museum of Western Art
        $cur_image_id = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'museum',
                'file_path' => '0000/17/2018/09/11/museum.jpg',
                'file_size' => '11382',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '275', 'file_height' => '183', 'created_at' => '2018-09-11 17:10:32'
            ]
        );

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => 'National Museum of Western Art',
                'content' => '1959年にオープンしたこの美術館は、国内で西洋美術に焦点を当てた唯一のアートギャラリーの1つです。 ほぼ5,000個のコレクションは、ルネサンスから20世紀まで続いています。

上野公園7-7、03-3828-5131、nmwa.go.jp 火曜日から日曜日の午前9時30分から午後5時30分まで（金曜日の午後8時）営業しています。 入場料は大人500円、大学生250円、高齢者および18歳未満の人は無料です。',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['agent'],
                'address' => '〒110-0007 東京都台東区上野公園７−7',
                'map_lat' => '35.71582346894051',
                'map_lng' => '139.77605099104596',
                'created_by' => $agent_id,
                'created_at' => '2018-09-11 18:01:44'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['agent'],
                'spot_id' => $spot_id,
                'day_order' => 0,
                'visit_order' => 2
            ]
        );

        // Tokyo Metropolitan Teien Art Museum
        $cur_image_id = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'metropolitan-teien-art-museum',
                'file_path' => '0000/17/2018/09/11/metropolitan-teien-art-museum.jpg',
                'file_size' => '8796',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '300', 'file_height' => '168', 'created_at' => '2018-09-11 17:42:21'
            ]
        );

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => 'Tokyo Metropolitan Teien Art Museum',
                'content' => '1933年に建てられたこの小さな博物館は、かつて朝香宮の公邸でした。 王子はパリで学び、アールデコ様式を日本に持ち込みたかったのです。これは建物の様式と装飾を説明しています。 1983年にこの邸宅は博物館になり、現在では一連の現代美術展が開催されています。

白金台5-21-9、03-3443-0201 03-3443-0201、teien-art-museum.ne.jp / 毎日午前10時から午後6時まで営業しています。 入場料は200円で、学生、子供、高齢者には割引があります。',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['agent'],
                'address' => '〒108-0071 東京都港区白金台５丁目２１−9',
                'map_lat' => '35.63696474275041',
                'map_lng' => '139.71901416778564',
                'created_by' => $agent_id,
                'created_at' => '2018-09-11 18:01:44'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['agent'],
                'spot_id' => $spot_id,
                'day_order' => 0,
                'visit_order' => 3
            ]
        );

        // Sensō-ji
        $cur_image_id = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'senso-ji',
                'file_path' => '0000/17/2018/09/11/senso-ji.jpg',
                'file_size' => '13518',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '299', 'file_height' => '168', 'created_at' => '2018-09-11 17:10:34'
            ]
        );

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => 'Sensō-ji',
                'content' => '東京の歴史的な宗教遺跡をチェックしたい場合は、浅草を散策するのに時間をかけることを忘れないでください。あなたが訪れることをお勧めする2つの場所は次のとおりです。

浅草寺–東京で最も人気のある有名な寺院です。美しくペイントされ、塔と美しい雷門の近くの景勝地にあります。メインホール内には、慈悲の女神である観音の巨大な像があります。 「木製の願い」カードが横に垂れ下がっているのを見てください。自分で書いて芸術的なディスプレイに参加できます。日中はとても忙しいので、夕方には敷地内をチェックしてみてください。 2浅草3丁目1番1号、台東区、03-3842-0181、senso-ji.jp敷地は24時間年中無休ですが、神殿自体は毎日午前6時から午後5時まで営業しています。入場は無料です。
浅草神社–この神社は浅草寺からそれほど遠くありません。これは浅草寺よりもはるかに平和です。人が少なく、人々が伝統的な儀式を祈ったり、瞑想したり、実行したりするのを見ることができます。江戸時代に建てられ、第二次世界大戦の空襲を生き延びました。 2台東3丁目1番1号浅草、台東、03-3844-1575、asakusajinja.jp毎日午前9時から午後4時30分まで営業しています。入場は無料です。',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['agent'],
                'address' => '〒111-0032 東京都台東区浅草２丁目３−1',
                'map_lat' => '35.71477534806532',
                'map_lng' => '139.7966480255127',
                'created_by' => $agent_id,
                'created_at' => '2018-09-11 18:01:44'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['agent'],
                'spot_id' => $spot_id,
                'day_order' => 0,
                'visit_order' => 4
            ]
        );

        // Imperial Palace
        $cur_image_id = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'imperial-palace',
                'file_path' => '0000/17/2018/09/11/imperial-palace.jpg',
                'file_size' => '10615',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '299', 'file_height' => '168', 'created_at' => '2018-09-11 17:10:34'
            ]
        );

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => 'Imperial Palace',
                'content' => '日本の皇帝の故郷である皇居は、日本の歴史と文化を学ぶのに最適な場所です。 以前は江戸城でしたが、15世紀に建てられ、当時の壁や堀の一部は現在でも使用されています。 1869年に天皇が京都から東京に移ったとき、彼は江戸を新しい宮殿に連れて行き、それを皇居と改名しました。

中に入ることはできませんが（または非常に近くに行くことはできませんが）、建物は素晴らしいです。 美しい敷地と公園に囲まれ、石垣の周りには堀があります。 また、比較的控えめで控えめですが、衛兵交代式の変更を見ることができます。 敷地内への入場は無料です。',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['agent'],
                'address' => '〒100-8111 東京都千代田区千代田１−1',
                'map_lat' => '35.68518697509635',
                'map_lng' => '139.75278854370117',
                'created_by' => $agent_id,
                'created_at' => '2018-09-11 18:01:44'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['agent'],
                'spot_id' => $spot_id,
                'day_order' => 1,
                'visit_order' => 0
            ]
        );

        // The National Art Center, Tokyo
        $cur_image_id = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'national-art-center',
                'file_path' => '0000/17/2018/09/11/national-art-center.jpg',
                'file_size' => '9872',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '275', 'file_height' => '183', 'created_at' => '2018-09-11 17:10:33'
            ]
        );

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => 'The National Art Center, Tokyo',
                'content' => '2007年にオープンしたこの博物館とギャラリーには、実際には永久的なコレクションはありませんが、印象派から現代美術まで、終わりのない一連の一時的な展覧会があります。 彼らのウェブサイトをチェックして、現在表示されているものを確認してください。

7六丁目22-2六本木、03-5777-8600、nact.jp 水曜日から月曜日の午前10時から午後5時30分まで営業しています。 入場料は展示物によって異なります。',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['agent'],
                'address' => '〒106-8558 東京都港区六本木７丁目２２−2',
                'map_lat' => '35.66531583024642',
                'map_lng' => '139.7263526916504',
                'created_by' => $agent_id,
                'created_at' => '2018-09-11 18:01:44'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['agent'],
                'spot_id' => $spot_id,
                'day_order' => 1,
                'visit_order' => 1
            ]
        );

        // Superhero Go-Karting
        $cur_image_id = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'street-cart',
                'file_path' => '0000/17/2018/09/12/street-cart.jpg',
                'file_size' => '14079',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '294', 'file_height' => '172', 'created_at' => '2018-09-11 17:10:33'
            ]
        );

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => 'Superhero Go-Karting',
                'content' => 'コスチュームを着たままゴーカートで東京の街を疾走したいですか？ もちろん、あなたはそうします！ MariCarは、マリオブラザーズの現実的なゴーカート会社で、街をドレスアップしてレースをすることができます。 国際運転免許証（有効な運転免許証があれば取得できます）があれば、参加できます。

外神田4-12-9、80-8899-8899 081、maricar.com / en / akihabara.html 毎日午前10時から午後10時まで営業しています。 コースは1〜2時間かかり、1人あたり9,000円かかります。 市内には複数の場所があります。',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['agent'],
                'address' => '〒140-0001 東京都品川区北品川１丁目２３−15',
                'map_lat' => '35.6210496181422',
                'map_lng' => '139.74142593829083',
                'created_by' => $agent_id,
                'created_at' => '2018-09-11 18:01:44'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['agent'],
                'spot_id' => $spot_id,
                'day_order' => 1,
                'visit_order' => 2
            ]
        );

        // Shinjuku Gyoen National Garden
        $cur_image_id = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'shinjuku-garden',
                'file_path' => '0000/17/2018/09/11/shinjuku-garden.jpg',
                'file_size' => '14045',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '275', 'file_height' => '183', 'created_at' => '2018-09-11 17:53:42'
            ]
        );

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => 'Shinjuku Gyoen National Garden',
                'content' => 'この公園は144エーカー以上で、約20,000本の木があります。 元の公園の大部分は、空襲中に第二次世界大戦で破壊されましたが、1949年に再建され、再開されました。

春には、この美しい公園は桜を見るのに最適な場所の1つです。 私のお気に入りの部分は、日本の風景庭園です。ここには、橋や島のある池がいくつかあります。 都会のh騒の中にある静かで小さなオアシスです。

新宿内藤町11、03-3350-0151、env.go.jp / garden / shinjukugyoen / index.html 火曜日から日曜日の午前9時から午後4時まで営業しています。 入場料は200円です。',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['agent'],
                'address' => '〒160-0014, 内藤町 新宿区 東京都 160-0014',
                'map_lat' => '35.68518697509635',
                'map_lng' => '139.71004486083984',
                'created_by' => $agent_id,
                'created_at' => '2018-09-11 18:01:44'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['agent'],
                'spot_id' => $spot_id,
                'day_order' => 2,
                'visit_order' => 0
            ]
        );

        // Shibuya Crossing
        $cur_image_id = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'shibuya-crossing',
                'file_path' => '0000/17/2018/09/11/shibuya-crossing.jpg',
                'file_size' => '16530',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '275', 'file_height' => '183', 'created_at' => '2018-09-11 17:56:06'
            ]
        );

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => 'Shibuya Crossing',
                'content' => 'これはおそらく世界で最も忙しくて最も有名な交差点です。 このエリアは、ステロイドのタイムズスクエアのように、明るい光と熱狂的なアクティビティで夜は賑やかです。 渋谷駅と交差点の間の像を必ず訪れてください。 映画「ハチ：犬の物語」で有名になった忠実な犬、ハチコへのオマージュです。',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['agent'],
                'address' => '〒150-0002 東京都渋谷区渋谷２丁目２４',
                'map_lat' => '35.6580743059596',
                'map_lng' => '139.70113773693913',
                'created_by' => $agent_id,
                'created_at' => '2018-09-11 18:01:44'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['agent'],
                'spot_id' => $spot_id,
                'day_order' => 2,
                'visit_order' => 1
            ]
        );

        // Robot Restaurant
        $cur_image_id = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'robot-restaurant',
                'file_path' => '0000/17/2018/09/11/robot-restaurant.jpg',
                'file_size' => '18144',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '271', 'file_height' => '186', 'created_at' => '2018-09-11 17:58:41'
            ]
        );

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => 'Robot Restaurant',
                'content' => 'このレストランは絶対的な感覚の過負荷です。 レーザー、ロボット、モンスター…すべて揃っています！ それは観光客のtrap（安くはない）かもしれませんが、これまでに見たことのないものとはまったく異なり、完全に普通とは違う何かをしたい場合には価格の価値があります！

1丁目7-1歌舞cho町、03-3200-5500 03-shinbot-robot.com/pc 平日は午後4時45分から1日3回公演が行われ、週末には午後のマチネーが行われます。',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['agent'],
                'address' => '〒160-0021 東京都新宿区歌舞伎町１丁目７−7 新宿ロボットビル B2F',
                'map_lat' => '35.70108475567244',
                'map_lng' => '139.70394771295832',
                'created_by' => $agent_id,
                'created_at' => '2018-09-11 18:01:44'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['agent'],
                'spot_id' => $spot_id,
                'day_order' => 2,
                'visit_order' => 2
            ]
        );

        // National Theatre of Japan
        $cur_image_id = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'national-theatre-of-japan',
                'file_path' => '0000/17/2018/09/11/national-theatre-of-japan.jpg',
                'file_size' => '7627',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '260', 'file_height' => '194', 'created_at' => '2018-09-11 17:58:41'
            ]
        );

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => 'National Theatre of Japan',
                'content' => '歌舞theaterは、ダンスや演劇を含む日本の伝統的な形式の演劇です。 コスチュームとメイクは非常に様式化されており、非常に視覚的なパフォーマンスを実現しています。 銀座にある歌舞z劇場は、これらの素晴らしいディスプレイの1つを見るのに最適な場所です。 ショー全体のチケットを購入することもできますし、より長いパフォーマンスにコミットする準備ができていない場合は1つのアクションのみを購入することもできます（日本語で数時間続く）。

4銀座12丁目15番15号、03-3545-6800、kabuki-za.co.jp パフォーマンスはほぼ毎日開催されます。 Webサイトで最新のスケジュールを確認してください。 シングルアクトチケットには少なくとも1,000円を支払うことを期待してください。',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['agent'],
                'address' => '〒102-8656 東京都千代田区隼町４−4-1',
                'map_lat' => '35.68159659061509',
                'map_lng' => '139.7433042526245',
                'created_by' => $agent_id,
                'created_at' => '2018-09-11 18:01:44'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['agent'],
                'spot_id' => $spot_id,
                'day_order' => 2,
                'visit_order' => 3
            ]
        );

        // 北海道大学植物園
        $cur_image_id = DB::table('media_files')->where('file_name', 'caption')->first()->id;

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => '北海道大学植物園',
                'content' => '北海道大学植物園は札幌市にある植物園で、1886年に開園した日本で2番目に古い植物園です。学生の教育や研究目的に設置されており、広さ13.3ヘクタールの園内には高山植物など約4,000種類の植物が育てられています。春には様々な花が咲き誇り、秋は紅葉が楽しめます。園内には博物館や北方民族資料室もあり、北海道の開拓についてや先住民族の生活・文化に関する資料が展示されています。電車でのアクセス：札幌駅(JR)など',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['operator'],
                'address' => '〒060-0003 北海道札幌市中央区北3条西8丁目北海道大学植物園内',
                'map_lat' => '43.06330958410495',
                'map_lng' => '141.345044465289',
                'created_by' => $operator_id,
                'created_at' => '2018-09-11 19:10:47'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['operator'],
                'spot_id' => $spot_id,
                'day_order' => 0,
                'visit_order' => 0
            ]
        );

        // さっぽろテレビ塔
        $cur_image_id = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'caption-1',
                'file_path' => '0000/18/2018/09/11/caption-1.jpg',
                'file_size' => '293878',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '1024', 'file_height' => '768', 'created_at' => '2018-09-11 18:41:04'
            ]
        );

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => 'さっぽろテレビ塔',
                'content' => 'さっぽろテレビ塔は、北海道札幌市にある高さ147.2メートルの電波塔です。札幌におけるテレビ放送開始を契機に1956年に建設されました。地上約90メートルの展望台からは、大通公園や日本海、石狩平野など札幌市全域を一望できます。展望台フロアにある”テレビ父さんのほほんパーク”は、テレビ塔の歴史がわかる休憩スペースになっています。電車でのアクセス：大通駅(札幌市営地下鉄東西線・南北線・東豊線) さっぽろ駅(JR)',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['operator'],
                'address' => '〒060-0042 北海道札幌市中央区大通西1',
                'map_lat' => '43.061112212338685',
                'map_lng' => '141.35640621185303',
                'created_by' => $operator_id,
                'created_at' => '2018-09-11 19:10:47'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['operator'],
                'spot_id' => $spot_id,
                'day_order' => 0,
                'visit_order' => 1
            ]
        );

        // 札幌中央卸売市場 場外市場
        $cur_image_id = DB::table('media_files')->where('file_name', 'caption-2')->first()->id;

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => '札幌中央卸売市場 場外市場',
                'content' => '北海道札幌市にある札幌中央卸売市場場外市場は札幌中央卸売市場の隣にあります。鮮魚店から青果店､乾物店に塩干物店など約60店舗ほどがあります。さらに寿司屋や定食屋などの飲食店もあります。飲食店は隣の札幌市中央卸売市場から新鮮な食材を料理して提供しています。お店は、早朝6時から17時まで営業していています。電車でのアクセス：二十四軒駅(地下鉄東西線)桑園駅(JR)',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['operator'],
                'address' => '〒060-0011 北海道札幌市中央区北11条西21-2-3',
                'map_lat' => '43.07073746018597',
                'map_lng' => '141.32074356079102',
                'created_by' => $operator_id,
                'created_at' => '2018-09-11 19:10:47'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['operator'],
                'spot_id' => $spot_id,
                'day_order' => 0,
                'visit_order' => 2
            ]
        );

        // 旭山動物園
        $cur_image_id = DB::table('media_files')->where('file_name', 'caption-3')->first()->id;

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => '旭山動物園',
                'content' => '1967年に開園した旭川市が運営する、2017年現在約15万2千平方メートルの広い敷地の中に107種類612もの動物がいる動物園です。動物の行動や生活を見せる「行動展示」という形式をとった動物園としても有名です。「もぐもぐタイム」や「夜の動物園」も人気です。施設内には動物資料展示館・動物図書館もあります。イベントも数多く実施されており教育活動にも力を入れています。電車でのアクセス：旭川駅(JR)からバスで約40分',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['operator'],
                'address' => '〒078-8205 北海道旭川市東旭川町倉沼',
                'map_lat' => '43.76861460074194',
                'map_lng' => '142.47997283935547',
                'created_by' => $operator_id,
                'created_at' => '2018-09-11 19:10:47'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['operator'],
                'spot_id' => $spot_id,
                'day_order' => 1,
                'visit_order' => 0
            ]
        );

        // 北海道エンジョイパス
        $cur_image_id = DB::table('media_files')->insertGetId(
            [
                'file_name' => 'd3',
                'file_path' => '0000/18/2018/09/11/d3.jpg',
                'file_size' => '35130',
                'file_type' => 'image/jpeg', 'file_extension' => 'jpg', 'create_user' => $agent_id,
                'file_width' => '540', 'file_height' => '360', 'created_at' => '2018-09-11 18:53:49'
            ]
        );

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => '北海道エンジョイパス',
                'content' => '数十頭のキタキツネとタヌキが巨大なオリで生活していて、観光客も中に入れる。人間慣れしているのか、後ろから近寄ってきて、荷物を奪って逃げるような習性があり、持ち物に注意。キツネは臆病なので襲ってくるようなことはない。',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['operator'],
                'address' => '〒091-0157 北海道北見市留辺蘂町花丘52-1',
                'map_lat' => '43.75479108241282',
                'map_lng' => '143.5008430480957',
                'created_by' => $operator_id,
                'created_at' => '2018-09-11 19:10:47'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['operator'],
                'spot_id' => $spot_id,
                'day_order' => 1,
                'visit_order' => 1
            ]
        );

        // ニングルテラス
        $cur_image_id = DB::table('media_files')->where('file_name', 'ningle-terrace')->first()->id;

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => 'ニングルテラス',
                'content' => '森の中を散策しながら、作家さんたちのオリジナル作品を扱うショップ巡りをするという感じの場所です。日が暮れてくると、ランプの明かりが良い雰囲気です。北海道らしい作品がたくさんありますが、ちょっとお高めなものが多い印象です。',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['operator'],
                'address' => '〒076-0016 北海道富良野市中御料',
                'map_lat' => '43.32334652289594',
                'map_lng' => '142.35654871920576',
                'created_by' => $operator_id,
                'created_at' => '2018-09-11 19:10:47'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['operator'],
                'spot_id' => $spot_id,
                'day_order' => 2,
                'visit_order' => 0
            ]
        );

        // とみたメロンハウス
        $cur_image_id = DB::table('media_files')->where('file_name', 'caption-4')->first()->id;

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => 'とみたメロンハウス',
                'content' => 'ただの商業施設。特に感動もしないし、まぁ、こんなもんでしょうと。休憩所として考えればわるくないけど、あえて行く必要もない…',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['operator'],
                'address' => '〒071-0714 北海道空知郡中富良野町宮町3-32',
                'map_lat' => '43.41773638968965',
                'map_lng' => '142.42686614806587',
                'created_by' => $operator_id,
                'created_at' => '2018-09-11 19:10:47'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['operator'],
                'spot_id' => $spot_id,
                'day_order' => 2,
                'visit_order' => 1
            ]
        );

        // 北海道中心標
        $cur_image_id = DB::table('media_files')->where('file_name', 'caption-5')->first()->id;

        // insert into itn_spots
        $spot_id = DB::table('itn_spots')->insertGetId(
            [
                'title' => '北海道中心標',
                'content' => '富良野市は北海道の中心地で、へその町として有名です。その中心の碑があると聞いて行ってみました。富良野神社の近くだというので神社に行くもそれらしいものはなく、向かいの小学校のグラウンドにありました。中心碑がドーンと置いてあるだけですが、北海道の真ん中！です。',
                'image_id' => $cur_image_id,
                'location_id' => $location_id['operator'],
                'address' => '〒076-0032 北海道富良野市若松町10',
                'map_lat' => '43.34455701126072',
                'map_lng' => '142.38367140997116',
                'created_by' => $operator_id,
                'created_at' => '2018-09-11 19:10:47'
            ]
        );

        //insert into itn_tour_has_spots
        DB::table('itn_tour_has_spots')->insert(
            [
                'tour_id' => $tour_id['operator'],
                'spot_id' => $spot_id,
                'day_order' => 2,
                'visit_order' => 2
            ]
        );

    }

    function changeAttributes($tour_id){
        // first delete attributes where 'service' field is tour
        DB::table('bravo_attrs')->where('service', 'tour')->delete();
        DB::table('bravo_tour_term')->delete();

        // now insert new attributes
        $a = new \Modules\Core\Models\Attributes([
            'name'=>'旅行の目的',
            'service'=>'tour'
        ]);

        $a->save();


        foreach (['ビジネス','ハネムーン','卒業旅行','自由旅行','特になし'] as $term){
            $t = new \Modules\Core\Models\Terms([
                'name'=>$term,
                'attr_id'=>$a->id
            ]);

            $t->save();
        }

        \Modules\Tour\Models\TourTerm::firstOrCreate([
            'term_id' => DB::table('bravo_terms')->where('name', '自由旅行')->first()->id,
            'tour_id' => $tour_id['agent']
        ]);

        \Modules\Tour\Models\TourTerm::firstOrCreate([
            'term_id' => DB::table('bravo_terms')->where('name', '特になし')->first()->id,
            'tour_id' => $tour_id['agent']
        ]);

        \Modules\Tour\Models\TourTerm::firstOrCreate([
            'term_id' => DB::table('bravo_terms')->where('name', '自由旅行')->first()->id,
            'tour_id' => $tour_id['operator']
        ]);

        \Modules\Tour\Models\TourTerm::firstOrCreate([
            'term_id' => DB::table('bravo_terms')->where('name', '特になし')->first()->id,
            'tour_id' => $tour_id['operator']
        ]);

        $a = new \Modules\Core\Models\Attributes([
            'name'=>'希望国＆エリア',
            'service'=>'tour'
        ]);
        $a->save();

        $term_ids = [];
        foreach (['北海道','沖縄','ヨーロッパ方面','イタリア','エストニア'] as $term){
            $t = new \Modules\Core\Models\Terms([
                'name'=>$term,
                'attr_id'=>$a->id
            ]);
            $t->save();
            $term_ids[] = $t->id;
        }

        \Modules\Tour\Models\TourTerm::firstOrCreate([
            'term_id' => DB::table('bravo_terms')->where('name', '北海道')->first()->id,
            'tour_id' => $tour_id['operator']
        ]);
    }

    function changeTourCatogoryNames(){
        DB::table('bravo_tour_category')->where('name', 'City trips')->update([
            'name' => '市内旅行'
        ]);
        DB::table('bravo_tour_category')->where('name', 'Ecotourism')->update([
            'name' => 'エコツーリズム'
        ]);
        DB::table('bravo_tour_category')->where('name', 'Escorted tour')->update([
            'name' => '護衛ツアー'
        ]);
        DB::table('bravo_tour_category')->where('name', 'Ligula')->update([
            'name' => 'リグラ'
        ]);
    }
}
