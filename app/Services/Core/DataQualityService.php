<?php

namespace App\Services\Core;

use App\Models\Customer;
use App\Models\DataQualityIssue;
use App\Models\Patient;
use App\Models\ProductService;
use App\Models\Vender;

class DataQualityService
{
    public function scanCustomerDuplicates(int $createdBy): int
    {
        $count = 0;
        $customers = Customer::query()
            ->where('created_by', $createdBy)
            ->whereNull('archived_at')
            ->whereNotNull('email')
            ->select('id', 'email', 'name')
            ->get()
            ->groupBy(fn (Customer $customer) => strtolower((string) $customer->email));

        foreach ($customers as $matches) {
            if ($matches->count() < 2) {
                continue;
            }

            $primary = $matches->first();
            foreach ($matches->slice(1) as $duplicate) {
                DataQualityIssue::updateOrCreate([
                    'created_by' => $createdBy,
                    'issue_type' => 'duplicate',
                    'module' => 'customers',
                    'record_type' => Customer::class,
                    'record_id' => $primary->id,
                    'duplicate_type' => Customer::class,
                    'duplicate_id' => $duplicate->id,
                ], [
                    'payload' => [
                        'primary_name' => $primary->name,
                        'duplicate_name' => $duplicate->name,
                        'email' => $primary->email,
                    ],
                    'status' => 'open',
                ]);
                $count++;
            }
        }

        return $count;
    }

    public function scanVenderDuplicates(int $createdBy): int
    {
        $count = 0;
        $venders = Vender::query()
            ->where('created_by', $createdBy)
            ->whereNull('archived_at')
            ->whereNotNull('email')
            ->select('id', 'email', 'name')
            ->get()
            ->groupBy(fn (Vender $vender) => strtolower((string) $vender->email));

        foreach ($venders as $matches) {
            if ($matches->count() < 2) {
                continue;
            }

            $primary = $matches->first();
            foreach ($matches->slice(1) as $duplicate) {
                DataQualityIssue::updateOrCreate([
                    'created_by' => $createdBy,
                    'issue_type' => 'duplicate',
                    'module' => 'venders',
                    'record_type' => Vender::class,
                    'record_id' => $primary->id,
                    'duplicate_type' => Vender::class,
                    'duplicate_id' => $duplicate->id,
                ], [
                    'payload' => [
                        'primary_name' => $primary->name,
                        'duplicate_name' => $duplicate->name,
                        'email' => $primary->email,
                    ],
                    'status' => 'open',
                ]);
                $count++;
            }
        }

        return $count;
    }

    public function scanProductServiceDuplicates(int $createdBy): int
    {
        $count = 0;
        $items = ProductService::query()
            ->where('created_by', $createdBy)
            ->whereNull('archived_at')
            ->whereNotNull('sku')
            ->select('id', 'sku', 'name')
            ->get()
            ->groupBy(fn (ProductService $item) => strtolower((string) $item->sku));

        foreach ($items as $matches) {
            if ($matches->count() < 2) {
                continue;
            }

            $primary = $matches->first();
            foreach ($matches->slice(1) as $duplicate) {
                DataQualityIssue::updateOrCreate([
                    'created_by' => $createdBy,
                    'issue_type' => 'duplicate',
                    'module' => 'product_services',
                    'record_type' => ProductService::class,
                    'record_id' => $primary->id,
                    'duplicate_type' => ProductService::class,
                    'duplicate_id' => $duplicate->id,
                ], [
                    'payload' => [
                        'primary_name' => $primary->name,
                        'duplicate_name' => $duplicate->name,
                        'sku' => $primary->sku,
                    ],
                    'status' => 'open',
                ]);
                $count++;
            }
        }

        return $count;
    }

    public function scanPatientDuplicates(int $createdBy): int
    {
        $count = 0;

        $patients = Patient::query()
            ->where('created_by', $createdBy)
            ->whereNull('archived_at')
            ->where(function ($query) {
                $query->whereNotNull('email')->orWhereNotNull('phone');
            })
            ->select('id', 'first_name', 'last_name', 'email', 'phone')
            ->get()
            ->groupBy(function (Patient $patient) {
                if (!empty($patient->email)) {
                    return 'email:' . strtolower((string) $patient->email);
                }

                return 'phone:' . preg_replace('/\D+/', '', (string) $patient->phone);
            });

        foreach ($patients as $matches) {
            if ($matches->count() < 2) {
                continue;
            }

            $primary = $matches->first();
            foreach ($matches->slice(1) as $duplicate) {
                DataQualityIssue::updateOrCreate([
                    'created_by' => $createdBy,
                    'issue_type' => 'duplicate',
                    'module' => 'patients',
                    'record_type' => Patient::class,
                    'record_id' => $primary->id,
                    'duplicate_type' => Patient::class,
                    'duplicate_id' => $duplicate->id,
                ], [
                    'payload' => [
                        'primary_name' => trim($primary->first_name . ' ' . $primary->last_name),
                        'duplicate_name' => trim($duplicate->first_name . ' ' . $duplicate->last_name),
                        'email' => $primary->email,
                        'phone' => $primary->phone,
                    ],
                    'status' => 'open',
                ]);
                $count++;
            }
        }

        return $count;
    }
}
