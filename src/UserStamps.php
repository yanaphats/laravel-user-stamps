<?php

namespace Sandbox\UserStamps;

trait UserStamps
{
    /**
     * Whether we're currently maintaining UserStamps.
     *
     * @param bool
     */
    protected $userStamping = true;

    /**
     * Boot the UserStamps trait for a model.
     *
     * @return void
     */
    public static function bootUserStamps()
    {
        static::addGlobalScope(new UserStampsScope);

        static::registerListeners();
    }

    /**
     * Register events we need to listen for.
     *
     * @return void
     */
    public static function registerListeners()
    {
        static::creating('Sandbox\UserStamps\Listeners\Creating@handle');
        static::updating('Sandbox\UserStamps\Listeners\Updating@handle');

        if (static::usingSoftDeletes()) {
            static::deleting('Sandbox\UserStamps\Listeners\Deleting@handle');
            static::restoring('Sandbox\UserStamps\Listeners\Restoring@handle');
        }
    }

    /**
     * Has the model loaded the SoftDeletes trait.
     *
     * @return bool
     */
    public static function usingSoftDeletes()
    {
        static $usingSoftDeletes;

        if (is_null($usingSoftDeletes)) {
            return $usingSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(get_called_class()));
        }

        return $usingSoftDeletes;
    }

    /**
     * Get the user that created the model.
     */
    public function creator()
    {
        return $this->belongsTo($this->getUserClass(), $this->getCreatedByColumn());
    }

    /**
     * Get the user that edited the model.
     */
    public function editor()
    {
        return $this->belongsTo($this->getUserClass(), $this->getUpdatedByColumn());
    }

    /**
     * Get the user that deleted the model.
     */
    public function destroyer()
    {
        return $this->belongsTo($this->getUserClass(), $this->getDeletedByColumn());
    }

    /**
     * Get the name of the "created by" column.
     *
     * @return string
     */
    public function getCreatedByColumn()
    {
        return defined('static::CREATED_BY') ? static::CREATED_BY : 'created_by';
    }

    /**
     * Get the name of the "updated by" column.
     *
     * @return string
     */
    public function getUpdatedByColumn()
    {
        return defined('static::UPDATED_BY') ? static::UPDATED_BY : 'updated_by';
    }

    /**
     * Get the name of the "deleted by" column.
     *
     * @return string
     */
    public function getDeletedByColumn()
    {
        return defined('static::DELETED_BY') ? static::DELETED_BY : 'deleted_by';
    }

    /**
     * Check if we're maintaining UserStamps on the model.
     *
     * @return bool
     */
    public function isUserStamping()
    {
        return $this->userStamping;
    }

    /**
     * Stop maintaining UserStamps on the model.
     *
     * @return void
     */
    public function stopUserStamping()
    {
        $this->userStamping = false;
    }

    /**
     * Start maintaining UserStamps on the model.
     *
     * @return void
     */
    public function startUserStamping()
    {
        $this->userStamping = true;
    }

    /**
     * Get the class being used to provide a User.
     *
     * @return string
     */
    protected function getUserClass()
    {
        return config('auth.providers.users.model');
    }
}
