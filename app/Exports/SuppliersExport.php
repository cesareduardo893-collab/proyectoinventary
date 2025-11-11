<?php

namespace App\Exports;

use Spatie\SimpleExcel\SimpleExcelWriter;

class SuppliersExport
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
            'Código',
            'Razón Social', 
            'RFC',
            'Tipo Proveedor',
            'Persona Contacto',
            'Teléfono Principal',
            'Correo Electrónico',
            'Teléfono Secundario',
            'Correo Secundario',
            'Información Comercial',
            'Horarios Atención',
            'Cuenta Bancaria',
            'Métodos Pago',
            'Plazos Crédito',
            'Dirección'
        ]);

        // Agregar datos
        foreach ($this->data as $row) {
            $writer->addRow([
                $row['codigo_proveedor'] ?? '',
                $row['nombre_razon_social'] ?? '',
                $row['rfc_identificacion_fiscal'] ?? 'N/A',
                $row['tipo_proveedor'] ?? 'N/A',
                $row['persona_contacto'] ?? 'N/A',
                $row['telefono_principal'] ?? 'N/A',
                $row['correo_electronico'] ?? 'N/A',
                $row['telefono_secundario'] ?? 'N/A',
                $row['correo_secundario'] ?? 'N/A',
                $row['informacion_comercial'] ?? 'N/A',
                $row['horarios_atencion'] ?? 'N/A',
                $row['cuenta_bancaria'] ?? 'N/A',
                $row['metodos_pago'] ?? 'N/A',
                $row['plazos_credito'] ?? 'N/A',
                $row['direccion'] ?? 'N/A'
            ]);
        }

        return $filePath;
    }
}