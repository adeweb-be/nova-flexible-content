<?php

namespace Whitecube\NovaFlexibleContent;

use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Correctly removes a file inside of a flexible layout
 *
 * @param Laravel\Nova\Http\Requests\NovaRequest $request
 * @param  $model
 * @return boolean
 */
function deleteFile(NovaRequest $request, $model, $field)
{
    $path = explode('.', $field->group->originalField);
    $path[] = $field->attribute;
    $mainField = array_shift($path);

    $data = $model->{$mainField};
    if(!is_array($data)){
        $data = json_decode($model->{$mainField});
    }
    $current = $data;
    foreach ($path as $i => $subpath) {
        if ($i === array_key_last($path))
            if(is_object($current)){
                $current->attributes->{$subpath} = "";
            } else {
                $current["attributes"][$subpath] = "";
            }
        elseif (is_array($current) && isset($current[$subpath])) {
            $current = $current[$subpath];
        } elseif (is_object($current) && isset($current->{$subpath})) {
            $current = $current->{$subpath};
        } else {
            abort(404);
        }
    };
    if(!is_array($model->{$mainField})){
        $model->{$mainField} = json_encode($data);
    } else {
        $model->{$mainField} = $data;
    }
    $model->{$mainField} = json_encode($data);
    $model->timestamps = false;
    $model->save();
    return true;
}
