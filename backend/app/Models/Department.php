<?php
// ╔══════════════════════════════════════════════════════════════╗
// ║  app/Models/Department.php                                   ║
// ╚══════════════════════════════════════════════════════════════╝
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'color'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}