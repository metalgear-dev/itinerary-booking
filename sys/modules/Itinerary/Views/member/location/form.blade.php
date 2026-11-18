<div class="form-group">
    <label>{{__("itinerary::location.form.name")}}</label>
    <input type="text" value="{{$translation->name}}" placeholder="{{__("itinerary::location.form.location_name")}}" name="name" class="form-control">
</div>
@if(is_default_lang())
    <div class="form-group">
        <label>{{__("itinerary::location.form.parent")}}</label>
        <select name="parent_id" class="form-control">
            <option value="">{{__("itinerary::location.form.please_select")}}</option>
            <?php
            $traverse = function ($categories, $prefix = '') use (&$traverse, $row) {
                foreach ($categories as $category) {
                    if ($category->id == $row->id) {
                        continue;
                    }
                    $selected = '';
                    if ($row->parent_id == $category->id)
                        $selected = 'selected';
                    printf("<option value='%s' %s>%s</option>", $category->id, $selected, $prefix . ' ' . $category->name);
                    $traverse($category->children, $prefix . '-');
                }
            };
            $traverse($parents);
            ?>
        </select>
    </div>
@endif
<div class="form-group">
    <label class="control-label">{{__("itinerary::location.form.description")}}</label>
    <div class="">
        <textarea name="content" class="d-none has-ckeditor" cols="30" rows="10">{{$translation->content}}</textarea>
    </div>
</div>
