<?php

namespace App\Exports;

use Spatie\SimpleExcel\SimpleExcelWriter;

class ProductsExport
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
            'ID',
            'Nombre',
            'Marca',
            'Modelo',
            'Descripción',
            'Precio',
            'Observaciones',
            'Categoría',
            'Proveedor',
            'Ubicación',
            'Unidad de Medida',
            'Número de Serie',
            'Cantidad Total',
            'Productos Prestados',
            'Cantidad Disponible',
            'Fecha de Registro'
        ]);

        // Agregar datos
        foreach ($this->data as $product) {
            $writer->addRow([
                $product['id'] ?? 'N/A',
                $product['name'] ?? 'N/A',
                $product['brand'] ?? 'N/A',
                $product['model'] ?? 'N/A',
                $product['description'] ?? 'N/A',
                $product['price'] != 0 ? '$' . number_format($product['price'], 2, '.', ',') : 'N/A',
                $product['observations'] ?? 'N/A',
                $product['category']['name'] ?? 'N/A',
                $product['supplier']['nombre_razon_social'] ?? 'N/A',
                $product['location'] ?? 'N/A',
                $product['measurement_unit'] ?? 'N/A',
                $product['serie'] ?? 'N/A',
                $product['quantity'] != 0 ? number_format($product['quantity'], 0, '.', ',') : 'N/A',
                $product['loaned_quantity'] ?? 0,
                $product['available_quantity'] ?? 0,
                isset($product['created_at']) ? 
                    \Carbon\Carbon::parse($product['created_at'])->format('Y-m-d H:i:s') : 'N/A'
            ]);
        }

        return $filePath;
    }
}