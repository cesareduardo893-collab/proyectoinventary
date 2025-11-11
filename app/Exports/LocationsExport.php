<?php

namespace App\Exports;

use Spatie\SimpleExcel\SimpleExcelWriter;

class LocationsExport
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function export(string $filePath)
    {
        $writer = SimpleExcelWriter::create($filePath, 'xlsx')
            ->noHeaderRow();

        // Agregar encabezados
        $writer->addRow([
            'Código Ubicación',
            'Sucursal', 
            'Sección Sucursal',
            'Estante',
            'Sección Estante',
            'Descripción',
            'Estado'
        ]);

        // Agregar datos
        foreach ($this->data as $row) {
            $writer->addRow([
                $row['codigo_ubicacion'] ?? '',
                $row['sucursal'] ?? '',
                $row['seccion_sucursal'] ?? 'N/A',
                $row['estante'] ?? 'N/A',
                $row['seccion_estante'] ?? 'N/A',
                $row['descripcion'] ?? 'N/A',
                $row['activo'] ? 'Activo' : 'Inactivo'
            ]);
        }

        return $filePath;
    }
}
