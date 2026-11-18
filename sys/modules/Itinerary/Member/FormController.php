<?php


namespace Modules\Itinerary\Member;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Itinerary\Controllers\ItineraryAdminController;
use Modules\Itinerary\Models\FormField;
use Modules\Itinerary\Models\InquiryForm;
use Modules\Itinerary\Models\ItineraryUser;

class FormController extends ItineraryAdminController {

    public function index() {
        $me = Auth::user();
        if (!$me->hasRole('agent')) {
            abort(403);
        }
        $form = InquiryForm::findOrCreateByUserId($me->id);
        $pre_defined_fields  = [
            [ 'name' => 'title',    'type' => 'text',       'placeholder' => 'title',   'attr' => 'required', 'priority' => 1 ],
            [ 'name' => 'name',     'type' => 'text',       'placeholder' => 'name',    'attr' => 'required' ],
            [ 'name' => 'email',    'type' => 'email',      'placeholder' => 'email'    ],
            [ 'name' => 'phone',    'type' => 'text',       'placeholder' => 'phone'    ],
            [ 'name' => 'calendar', 'type' => 'daterange',  'placeholder' => 'calendar' ],
            [ 'name' => 'inquiry',  'type' => 'textarea',       'placeholder' => 'inquiry', 'attr' => 'required' ]
        ];
        $field_types = [
            ['title' => t('itinerary::form.type', 'text'),      'value' => 'text'],
            ['title' => t('itinerary::form.type', 'date'),      'value' => 'date'],
            ['title' => t('itinerary::form.type', 'datetime'),  'value' => 'datetime-local'],
            ['title' => t('itinerary::form.type', 'url'),       'value' => 'url'],
            ['title' => t('itinerary::form.type', 'email'),     'value' => 'email'],
            ['title' => t('itinerary::form.type', 'daterange'), 'value' => 'daterange']
        ];
        $data = [
            'me' => $me,
            'form' => $form,
            'pre_defined_fields' => $pre_defined_fields,
            'field_types' => $field_types,
            'fields' => $form->fields()->get() ?? []
        ];
        return view('Itinerary::member.agent.form.index', $data);
    }

    public function preview(Request $request) {
        $form = new \stdClass();
        $form->image_id = $request->input('image_id');
        $form->content = $request->input('content');
        $form->afk_message = $request->input('afk_message');
        $fields = $request->input('fields') ? json_decode($request->input('fields')) : [];
        $data = [
            'form' => $form,
            'fields' => $fields,
            'breadcrumbs' => [
                [
                    'name' => __('itinerary::dashboard.sidebar.form'),
                    'url' => 'itinerary/member/form'
                ],[
                    'name' => __('Preview'),
                    'class' => 'active'
                ]
            ]
        ];
        return view('Itinerary::member.agent.form.preview', $data);
    }

    public function store(Request $request) {
        if ($request->input('preview') == true) {
            return $this->preview($request);
        }
        $me = Auth::user();
        if (!$me->hasRole('agent')) {
            abort(403);
        }
        $fields = json_decode($request->input('fields'));
        if (!$fields) {
            return back()->with('error', __('Fields cannot be empty'));
        }
        $form = InquiryForm::findOrCreateByUserId($me->id);
        $form->image_id = $request->input('image_id');
        $form->content = $request->input('content');
        $form->afk_message = $request->input('afk_message');
        if (!($form->save())) {
            return back()->with('error', __('Save failed.'));
        }
        $form->fields()->forceDelete();
        foreach($fields as $field) {
            $form_field = new FormField();
            foreach(['name', 'type', 'placeholder', 'attr', 'priority'] as $key) {
                if (isset($field->$key)) {
                    $form_field->$key = $field->$key;
                }
                $form_field->form_id = $form->id;
            }
            $form_field->save();
        }
        return back()->with('success', __("Updated successfully!"));
    }



}
