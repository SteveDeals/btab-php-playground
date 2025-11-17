<?php

namespace Btab\Jobs;

use Btab\Http\BtabClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ImportProductsJob implements ShouldQueue
{
    use Queueable;

    public function handle()
    {
        Log::info("Starting BTAB product import...");
        $client = new BtabClient();
        $page = 1;
        $products = [];

        do {
            $data = $client->fetchProducts($page);
            if (empty($data['items'])) break;

            foreach ($data['items'] as $item) {
                $validator = Validator::make($item, [
                    'id' => 'required',
                    'sku' => 'required|string',
                    'name' => 'required|string|max:255',
                    'price' => 'required|numeric',
                    'currency' => 'required|string|size:3',
                ]);

                if ($validator->fails()) {
                    Log::warning('Validation failed for BTAB product', ['item' => $item]);
                    continue;
                }

                $products[] = [
                    'product.code' => $item['sku'],
                    'product.label' => $item['name'],
                    'product.status' => 1,
                    'product.type' => 'default',
                    'product.stock' => $item['stock'] ?? 0,
                    'product.price' => $item['price'],
                    'product.currencyid' => $item['currency'],
                    'product.datestart' => now()->toDateString(),
                ];
            }

            $page++;
        } while (!empty($data['items']));

        if (empty($products)) {
            Log::warning('No valid products found from BTAB API.');
            return;
        }

        // Save CSV to temp storage
        $filename = 'btab_import_' . time() . '.csv';
        $path = storage_path("app/{$filename}");
        $fp = fopen($path, 'w');

        fputcsv($fp, array_keys($products[0]));
        foreach ($products as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);

        // Call Aimeos product import job controller
        $context = app('aimeos.context')->get(false, 'command');
        $controller = \Aimeos\Controller\Jobs\Factory::createController($context, 'product/import/csv');
        $controller->setFile($path);
        $controller->run();

        Log::info('BTAB product import completed successfully.');
    }
}
