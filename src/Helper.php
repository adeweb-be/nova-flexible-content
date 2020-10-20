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

    $data = json_decode($model->{$mainField});

    $current = $data;
    foreach ($path as $i => $subpath) {
        if ($i === array_key_last($path))
            $current->attributes->{$subpath} = "";
        elseif (is_array($current) && isset($current[$subpath])) {
            $current = $current[$subpath];
        } elseif (is_object($current) && isset($current->{$subpath})) {
            $current = $current->{$subpath};
        } else {
            abort(404);
        }
    };
    $model->{$mainField} = json_encode($data);
    $model->timestamps = false;
    $model->save();
    $model->timestamps = true;

    return true;
}
