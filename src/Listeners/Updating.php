<?php

namespace Sandbox\UserStamps\Listeners;

use Illuminate\Support\Facades\Auth;

class Updating
{
    /**
     * When the model is being updated.
     *
     * @param  Illuminate\Database\Eloquent  $model
     * @return void
     */
    public function handle($model)
    {
        if (! $model->isUserStamping() || is_null($model->getUpdatedByColumn()) || is_null(Auth::id())) {
            return;
        }

        $model->{$model->getUpdatedByColumn()} = Auth::id();
    }
}
