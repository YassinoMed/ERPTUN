# Extraction microservices POS / Inventory / Accounting / Sales / WMS / Production

## Découpage et responsabilités

- POS : ventes en caisse, tickets, paiements POS, sorties de stock POS
- Inventory : produits, dépôts, stocks par dépôt, transferts de stock
- Accounting : factures, bills, dépenses, écritures de journal
- Sales : devis, propositions commerciales, conversions
- WMS : entrepôts, stocks par emplacement, suivi des mouvements
- Production : ordres, gammes, centres de travail

## Endpoints extraits

- POS
  - GET /api/pos
  - GET /api/pos/{pos}
- Inventory
  - GET /api/inventory/products
  - GET /api/inventory/products/{productService}
  - GET /api/inventory/warehouses
  - GET /api/inventory/warehouses/{warehouse}
  - GET /api/inventory/warehouse-products
  - GET /api/inventory/warehouse-products/{warehouseProduct}
- Accounting
  - GET /api/accounting/invoices
  - GET /api/accounting/invoices/{invoice}
  - GET /api/accounting/bills
  - GET /api/accounting/bills/{bill}
  - GET /api/accounting/expenses
  - GET /api/accounting/expenses/{expense}
  - GET /api/accounting/journals
  - GET /api/accounting/journals/{journalEntry}
- Sales
  - GET /api/sales/quotations
  - GET /api/sales/quotations/{quotation}
  - GET /api/sales/proposals
  - GET /api/sales/proposals/{proposal}
- WMS
  - GET /api/wms/warehouses
  - GET /api/wms/warehouses/{warehouse}
  - GET /api/wms/warehouse-products
  - GET /api/wms/warehouse-products/{warehouseProduct}
  - GET /api/wms/stock-reports
- Production
  - GET /api/production/work-centers
  - GET /api/production/boms
  - GET /api/production/orders
  - GET /api/production/orders/{productionOrder}

## Services et ports

- Gateway : http://localhost:8080
- POS : http://localhost:8005
- Inventory : http://localhost:8006
- Accounting : http://localhost:8007
- Sales : http://localhost:8008
- WMS : http://localhost:8009
- Production : http://localhost:8010

## Variables d’environnement clés

- POS_DB_CONNECTION=pos
- INVENTORY_DB_CONNECTION=inventory
- ACCOUNTING_DB_CONNECTION=accounting
- SALES_DB_CONNECTION=sales
- WMS_DB_CONNECTION=wms
- PRODUCTION_DB_CONNECTION=production
- POS_QUEUE_CONNECTION=redis
- INVENTORY_QUEUE_CONNECTION=redis
- ACCOUNTING_QUEUE_CONNECTION=redis
- SALES_QUEUE_CONNECTION=redis
- WMS_QUEUE_CONNECTION=redis
- PRODUCTION_QUEUE_CONNECTION=redis
- POS_EVENT_ENDPOINT, INVENTORY_EVENT_ENDPOINT, ACCOUNTING_EVENT_ENDPOINT, SALES_EVENT_ENDPOINT, WMS_EVENT_ENDPOINT, PRODUCTION_EVENT_ENDPOINT

## Validation intégration

- php artisan test --filter=PosApiTest
- php artisan test --filter=InventoryApiTest
- php artisan test --filter=AccountingApiTest
- php artisan test --filter=SalesApiTest
- php artisan test --filter=WmsApiTest
- php artisan test --filter=ProductionApiTest

## Validation charge

- Installer k6
- Exemple POS :
  - k6 run -e BASE_URL=http://localhost:8080/pos load-tests/pos-load.js
- Exemple Inventory :
  - k6 run -e BASE_URL=http://localhost:8080/inventory load-tests/inventory-load.js
- Exemple Accounting :
  - k6 run -e BASE_URL=http://localhost:8080/accounting load-tests/accounting-load.js
- Exemple Sales :
  - k6 run -e BASE_URL=http://localhost:8080/sales load-tests/sales-load.js
- Exemple WMS :
  - k6 run -e BASE_URL=http://localhost:8080/wms load-tests/wms-load.js
- Exemple Production :
  - k6 run -e BASE_URL=http://localhost:8080/production load-tests/production-load.js
