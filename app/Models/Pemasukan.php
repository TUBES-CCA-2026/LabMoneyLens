<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    protected $table = 'pemasukan';
    protected $primaryKey = 'id_pemasukan';

    public function jenisPenerimaan()
    {
        return $this->belongsTo(JenisPenerimaan::class, 'id_jenis_penerimaan', 'id_jenis_penerimaan');
    }
}
