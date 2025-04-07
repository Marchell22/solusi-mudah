<?php
// app/Traits/Auditable.php
namespace App\Traits;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    /**
     * Boot the trait
     */
    public static function bootAuditable()
    {
        // Menangkap event created
        static::created(function (Model $model) {
            $model->auditEvent('created');
        });

        // Menangkap event updated
        static::updated(function (Model $model) {
            $model->auditEvent('updated');
        });

        // Menangkap event deleted
        static::deleted(function (Model $model) {
            $model->auditEvent('deleted');
        });

        // Menangkap event restored (jika menggunakan SoftDeletes)
        if (method_exists(static::class, 'restored')) {
            static::restored(function (Model $model) {
                $model->auditEvent('restored');
            });
        }
    }

    /**
     * Membuat audit log untuk sebuah event
     *
     * @param string $event
     * @return void
     */
    protected function auditEvent(string $event)
    {
        // Mendapatkan atribut yang dapat di-audit
        $auditableFields = $this->getAuditableFields();
        
        // Jika tidak ada field yang bisa diaudit, return
        if (empty($auditableFields)) {
            return;
        }

        // Siapkan data untuk audit
        $oldValues = [];
        $newValues = [];

        if ($event === 'created') {
            foreach ($auditableFields as $field) {
                if (array_key_exists($field, $this->getAttributes())) {
                    $newValues[$field] = $this->getAttribute($field);
                }
            }
        } elseif ($event === 'updated') {
            foreach ($auditableFields as $field) {
                if ($this->isDirty($field)) {
                    $oldValues[$field] = $this->getOriginal($field);
                    $newValues[$field] = $this->getAttribute($field);
                }
            }
            
            // Jika tidak ada perubahan pada field yang diaudit, return
            if (empty($oldValues) && empty($newValues)) {
                return;
            }
        } elseif ($event === 'deleted') {
            foreach ($auditableFields as $field) {
                if (array_key_exists($field, $this->getAttributes())) {
                    $oldValues[$field] = $this->getAttribute($field);
                }
            }
        } elseif ($event === 'restored') {
            foreach ($auditableFields as $field) {
                if (array_key_exists($field, $this->getAttributes())) {
                    $newValues[$field] = $this->getAttribute($field);
                }
            }
        }

        // Buat audit record
        Audit::create([
            'user_id' => Auth::id(),
            'auditable_id' => $this->getKey(),
            'auditable_type' => get_class($this),
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'url' => Request::fullUrl(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Mendapatkan fields yang bisa diaudit
     * 
     * @return array
     */
    protected function getAuditableFields(): array
    {
        // Gunakan property $auditableFields jika ada, jika tidak gunakan $fillable
        if (property_exists($this, 'auditableFields') && is_array($this->auditableFields)) {
            return $this->auditableFields;
        }

        return $this->fillable;
    }

    /**
     * Relasi ke audit logs
     */
    public function audits()
    {
        return $this->morphMany(Audit::class, 'auditable');
    }
}