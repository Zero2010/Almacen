<?php $__env->startSection('title', 'Realizar Pedido - Comida al paso'); ?>
<?php $__env->startSection('body_class', 'sub_page'); ?>

<?php $__env->startSection('styles'); ?>
<style>
  :root {
    --page-bg: #07090d;
    --glass-bg: #111620;
    --glass-border: rgba(255,255,255,0.08);
    --text-primary: #f5f6f8;
    --text-muted: rgba(255,255,255,0.60);
    --accent: #ffc94d;
    --accent-hover: #ffb800;
    --accent-dim: rgba(255,201,77,0.12);
    --accent-border: rgba(255,201,77,0.30);
    --danger: #ff5f73;
    --danger-bg: rgba(255,95,115,0.12);
    --radius-lg: 20px;
    --radius-md: 14px;
    --radius-sm: 10px;
  }

  .pedidos-page {
    padding: 50px 0 70px;
    background: var(--page-bg);
    min-height: calc(100vh - 180px);
  }

  .page-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--accent-dim);
    border: 1px solid var(--accent-border);
    padding: 8px 16px;
    border-radius: 30px;
    font-size: 0.80rem;
    font-weight: 600;
    color: var(--accent);
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .section-title {
    font-size: 2.2rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 6px;
    letter-spacing: -0.4px;
  }

  .section-subtitle {
    color: var(--text-muted);
    font-size: 1rem;
    margin-bottom: 32px;
  }

  .card-dark {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg);
    box-shadow: 0 8px 32px rgba(0,0,0,0.40);
    backdrop-filter: blur(8px);
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .card-dark:hover {
    border-color: rgba(255,255,255,0.14);
  }

  .card-section-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--accent-dim);
    border: 1px solid var(--accent-border);
    padding: 7px 14px;
    border-radius: 20px;
    font-size: 0.80rem;
    font-weight: 600;
    color: var(--accent);
    margin-bottom: 16px;
  }

  .form-control {
    background-color: rgba(255,255,255,0.06) !important;
    border: 1px solid rgba(255,255,255,0.14) !important;
    color: #fff !important;
    border-radius: var(--radius-sm) !important;
    height: 46px;
    font-size: 0.95rem;
    transition: all 0.2s;
  }
  .form-control:focus {
    background-color: rgba(255,255,255,0.10) !important;
    border-color: var(--accent) !important;
    box-shadow: 0 0 0 3px rgba(255,201,77,0.10);
  }
  .form-control::placeholder { color: rgba(255,255,255,0.40) !important; }
  textarea.form-control { height: auto; min-height: 80px; resize: vertical; }
  select.form-control {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='rgba(255,255,255,0.6)' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    background-size: 12px;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    padding-right: 36px !important;
    background-color: rgba(255,255,255,0.06) !important;
    color: #fff !important;
    border: 1px solid rgba(255,255,255,0.14) !important;
  }
  select.form-control option {
    background: #141720 !important;
    color: #fff !important;
  }
  select.form-control:disabled {
    background-color: rgba(255, 255, 255, 0.03) !important;
    opacity: 0.55;
    cursor: not-allowed;
    color: rgba(255, 255, 255, 0.45) !important;
  }

  /* Selector de Región y Comuna en grilla 2 columnas */
  .delivery-locality-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
  }
  @media (max-width: 480px) {
    .delivery-locality-row {
      grid-template-columns: 1fr;
    }
  }

  /* Indicador de carga en select de comuna */
  .commune-loading {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23ffc94d' viewBox='0 0 16 16'%3E%3Cpath d='M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0L.534 9.41a.25.25 0 0 1 .192-.41zM3.467 11h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0L2.533 13.41a.25.25 0 0 1 .192-.41z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    background-size: 14px;
  }

  .invalid-feedback-custom {
    display: none;
    font-size: 0.78rem;
    color: #ef4444;
    margin-top: 4px;
    font-weight: 600;
  }
  .is-invalid-custom {
    border-color: #ef4444 !important;
    background-color: rgba(255,95,115,0.10) !important;
  }
  .is-invalid-custom + .invalid-feedback-custom,
  .is-invalid-custom ~ .invalid-feedback-custom {
    display: block;
  }

  .input-group-text {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.16);
    border-right: none;
    color: rgba(255,255,255,0.85);
    font-weight: 700;
    border-radius: var(--radius-sm) 0 0 var(--radius-sm);
  }
  .input-group .form-control {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
  }

  .form-label {
    font-weight: 600;
    font-size: 0.82rem;
    color: rgba(255,255,255,0.85);
    margin-bottom: 6px;
  }

  .products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 14px;
    max-height: 400px;
    overflow-y: auto;
    padding-right: 4px;
    align-content: start;
  }
  .products-grid::-webkit-scrollbar { width: 5px; }
  .products-grid::-webkit-scrollbar-track { background: transparent; }
  .products-grid::-webkit-scrollbar-thumb { background: rgba(255,201,77,0.3); border-radius: 10px; }

  .product-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: var(--radius-md);
    padding: 14px;
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
  }
  .product-card:hover {
    background: rgba(255,201,77,0.10);
    border-color: rgba(255,201,77,0.35);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(255,190,51,0.12);
  }
  .product-card.added {
    background: rgba(255,201,77,0.14);
    border-color: var(--accent);
    box-shadow: 0 0 0 1px rgba(255,190,51,0.10);
  }
  .product-card img {
    width: 56px;
    height: 56px;
    object-fit: cover;
    border-radius: 10px;
    background: rgba(255,255,255,0.06);
    flex-shrink: 0;
  }
  .product-info { flex: 1; min-width: 0; }
  .product-info h6 {
    font-size: 0.92rem;
    font-weight: 600;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #fff;
  }
  .product-info .price {
    color: var(--accent);
    font-weight: 700;
    font-size: 0.88rem;
    margin-top: 3px;
    display: block;
  }
  .product-info .qty-badge {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-top: 2px;
    display: block;
  }
  .product-card.added .product-info .qty-badge { color: var(--accent); }

  .btn-product {
    background: var(--accent);
    color: #111;
    font-weight: 700;
    padding: 7px 14px;
    border-radius: var(--radius-sm);
    border: none;
    font-size: 0.88rem;
    transition: all 0.2s;
    flex-shrink: 0;
    white-space: nowrap;
  }
  .btn-product:hover {
    background: var(--accent-hover);
    transform: scale(1.04);
  }
  .product-card.in-cart .btn-product {
    background: rgba(255,255,255,0.10);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.20);
  }
  .product-card.in-cart .btn-product:hover {
    background: var(--danger-bg);
    color: var(--danger);
    border-color: var(--danger);
  }

  .cart-wrapper {
    background: rgba(255,201,77,0.03);
    border: 1px solid rgba(255,201,77,0.14);
    border-radius: var(--radius-lg);
    overflow: hidden;
    margin-top: 16px;
  }

  .cart-header {
    padding: 16px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .cart-table th {
    font-size: 0.72rem;
    text-transform: uppercase;
    color: var(--text-muted);
    font-weight: 600;
    padding: 12px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    letter-spacing: 0.4px;
    background: transparent;
  }
  .cart-table td {
    padding: 12px;
    vertical-align: middle;
    color: #fff;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    background: transparent;
  }
  .cart-table tbody tr {
    transition: background 0.15s;
  }
  .cart-table tbody tr:hover td {
    background: rgba(255,255,255,0.02);
  }
  .cart-table .empty-cart-row td {
    padding: 40px 20px;
    background: transparent !important;
  }
  .cart-table .empty-cart-msg {
    text-align: center;
    color: var(--text-muted);
  }
  .cart-table .empty-cart-msg i {
    font-size: 2.6rem;
    display: block;
    margin-bottom: 10px;
    color: rgba(255,255,255,0.20);
  }
  .cart-table .empty-cart-msg p { margin: 0; }

  @keyframes slideIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .cart-item-row { animation: slideIn 0.2s ease; }

  .qty-input-group {
    display: inline-flex;
    align-items: center;
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 8px;
    overflow: hidden;
    background: rgba(255,255,255,0.04);
    height: 32px;
  }
  .qty-btn {
    background: transparent;
    border: none;
    color: #fff;
    width: 30px;
    height: 32px;
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.15s;
  }
  .qty-btn:hover { background: rgba(255,201,77,0.30); }
  .qty-value {
    width: 38px;
    text-align: center;
    background: transparent;
    border: none;
    border-left: 1px solid rgba(255,255,255,0.10);
    border-right: 1px solid rgba(255,255,255,0.10);
    color: #fff;
    font-weight: 700;
    font-size: 0.88rem;
    padding: 4px 0;
    -moz-appearance: textfield;
  }
  .qty-value::-webkit-outer-spin-button,
  .qty-value::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

  .btn-remove {
    background: transparent;
    border: none;
    color: rgba(255,100,100,0.65);
    width: 32px;
    height: 32px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
  .btn-remove:hover {
    background: var(--danger-bg);
    color: var(--danger);
  }

  .cart-summary {
    padding: 18px 20px;
    border-top: 1px solid rgba(255,255,255,0.08);
    background: rgba(0,0,0,0.20);
  }
  .cart-summary .total-row {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--accent);
    border-top: 1px dashed rgba(255,255,255,0.18);
    padding-top: 10px;
    margin-top: 8px;
  }

  .btn-submit {
    background: linear-gradient(135deg, var(--accent), #e6a520);
    color: #111;
    font-weight: 700;
    font-size: 0.95rem;
    padding: 14px;
    border-radius: var(--radius-sm);
    border: none;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    transition: all 0.2s;
    box-shadow: 0 6px 20px rgba(255,190,51,0.25);
  }
  .btn-submit:hover:not(:disabled) {
    background: linear-gradient(135deg, var(--accent-hover), #cc8f00);
    transform: translateY(-1px);
    box-shadow: 0 10px 28px rgba(255,190,51,0.35);
  }
  .btn-submit:disabled {
    opacity: 0.40;
    cursor: not-allowed;
    filter: grayscale(20%);
  }

  .info-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(59,179,227,0.10);
    border: 1px solid rgba(59,179,227,0.22);
    color: #8cd9f0;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.78rem;
    margin-top: 8px;
  }

  .cart-count-badge {
    background: rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.75);
    font-size: 0.78rem;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 20px;
  }

  @media (max-width: 991px) {
    .section-title { font-size: 1.7rem; }
    .products-grid { grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); max-height: 300px; }
  }
  @media (max-width: 767px) {
    .pedidos-page { padding: 30px 0 50px; }
    .products-grid { grid-template-columns: 1fr 1fr; max-height: 240px; }
    .product-card { padding: 10px; gap: 10px; }
    .product-card img { width: 44px; height: 44px; }
    .cart-table th:nth-child(3),
    .cart-table td:nth-child(3) { display: none; }
  }
  @media (max-width: 480px) {
    .products-grid { grid-template-columns: 1fr; max-height: none; }
    .section-title { font-size: 1.4rem; }
  }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- DEBUG: Region/Comune selectors added on 2026-06-06 -->
<section class="pedidos-page">
  <div class="container">
    <div class="text-center mb-4">
      <div class="page-eyebrow"><i class="fa fa-cutlery"></i> Pide online</div>
      <h1 class="section-title">Haz tu Pedido Online</h1>
      <p class="section-subtitle">Elige tus platos favoritos, completa tus datos y listo.</p>
    </div>

    <form action="<?php echo e(route('pedidos.store')); ?>" method="POST" id="orderForm" class="row g-4">
      <?php echo csrf_field(); ?>
      <!-- Izquierda: Formulario de datos -->
      <div class="col-lg-5">
        <div class="card-dark h-100">
          <div class="card-body p-4">
            <div class="card-section-label"><i class="fa fa-user-circle"></i> Datos de Contacto</div>

            <div class="form-group mb-3">
              <label for="client_name" class="form-label">Nombre Completo</label>
              <input type="text" name="client_name" id="client_name" class="form-control" placeholder="Ej. Juan Pérez" value="<?php echo e(old('client_name')); ?>" required>
            </div>

            <div class="form-group mb-3">
              <label for="client_phone" class="form-label">Teléfono (9 dígitos)</label>
              <div class="input-group">
                <span class="input-group-text">+56</span>
                <input type="text" name="client_phone" id="client_phone" class="form-control" placeholder="9 1234 5678" value="<?php echo e(old('client_phone')); ?>" required>
              </div>
            </div>

            <div class="form-group mb-3">
              <label for="client_email" class="form-label">Correo Electrónico <small class="text-muted fw-normal" style="font-size:0.75rem;">(opcional, para boleta)</small></label>
              <input type="email" name="client_email" id="client_email" class="form-control" placeholder="juan.perez@email.com" value="<?php echo e(old('client_email')); ?>">
            </div>

            <hr class="border-secondary my-4" style="border-color: rgba(255,255,255,0.10) !important;">

            <div class="card-section-label"><i class="fa fa-truck"></i> Opciones de Entrega</div>

            <div class="form-group mb-3">
              <label for="type" class="form-label">Tipo de Pedido</label>
              <select name="type" id="type" class="form-control" onchange="toggleDeliveryFields()" required>
                <option value="delivery" <?php echo e(old('type', 'delivery') === 'delivery' ? 'selected' : ''); ?>>🚚 Delivery a Domicilio</option>
                <option value="pickup" <?php echo e(old('type') === 'pickup' ? 'selected' : ''); ?>>📦 Retiro en Local</option>
                <option value="in_person" <?php echo e(old('type') === 'in_person' ? 'selected' : ''); ?>>🍽️ Presencial (Mesa)</option>
              </select>
            </div>

            <div id="deliveryFields">
              <div class="delivery-locality-row">
                <div class="form-group mb-3">
                  <label for="region_id" class="form-label">Región <span class="required-star">*</span></label>
                  <select name="region_id" id="region_id" class="form-control" onchange="onRegionChange(this)" style="border: 2px solid red !important;">
                    <option value="">Seleccione una región</option>
                    <?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($region->id); ?>" <?php echo e(old('region_id') == $region->id ? 'selected' : ''); ?>>
                        <?php echo e($region->name); ?>

                      </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                  <div class="invalid-feedback-custom" id="region_id_error">Seleccione una región válida.</div>
                </div>

                <div class="form-group mb-3">
                  <label for="commune_id" class="form-label">Comuna <span class="required-star">*</span></label>
                  <select name="commune_id" id="commune_id" class="form-control">
                    <option value="">Seleccione una comuna</option>
                    <?php if(old('region_id')): ?>
                      <?php
                        $oldRegion = \App\Models\Region::with('communes')->find(old('region_id'));
                      ?>
                      <?php if($oldRegion): ?>
                        <?php $__currentLoopData = $oldRegion->communes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commune): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option value="<?php echo e($commune->id); ?>" <?php echo e(old('commune_id') == $commune->id ? 'selected' : ''); ?>>
                            <?php echo e($commune->name); ?>

                          </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php endif; ?>
                    <?php endif; ?>
                  </select>
                  <div class="invalid-feedback-custom" id="commune_id_error">Seleccione una comuna válida.</div>
                </div>
              </div>

              <div class="form-group mb-3">
                <label for="address" class="form-label">Dirección de Despacho <span class="required-star">*</span></label>
                <input type="text" name="address" id="address" class="form-control" placeholder="Calle, Número, Depto..." value="<?php echo e(old('address')); ?>">
                <div class="invalid-feedback-custom" id="address_error">Ingrese la dirección de despacho.</div>
              </div>
              <span class="info-pill"><i class="fa fa-info-circle"></i> Recargo fijo de $2.000 CLP por delivery</span>
            </div>

            <div id="inPersonFields" class="d-none">
              <div class="form-group mb-3">
                <label for="table_id" class="form-label">Mesa Disponible</label>
                <select name="table_id" id="table_id" class="form-control">
                  <option value="">Selecciona una mesa...</option>
                  <?php $__currentLoopData = $tables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($table->id); ?>" <?php echo e(old('table_id') == $table->id ? 'selected' : ''); ?>>
                      Mesa #<?php echo e($table->number); ?> (Capacidad: <?php echo e($table->capacity); ?>)</option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($tables->isEmpty()): ?>
                  <small class="text-muted" style="font-size: 0.75rem;">No hay mesas disponibles en este momento.</small>
                <?php endif; ?>
              </div>
            </div>

            <hr class="border-secondary my-4" style="border-color: rgba(255,255,255,0.10) !important;">

            <div class="card-section-label"><i class="fa fa-credit-card"></i> Pago y Facturación</div>

            <div class="form-group mb-3">
              <label for="payment_method" class="form-label">Método de Pago</label>
              <select name="payment_method" id="payment_method" class="form-control" required>
                <option value="transbank" <?php echo e(old('payment_method') === 'transbank' ? 'selected' : ''); ?>>💳 Tarjeta (Transbank)</option>
                <option value="transfer" <?php echo e(old('payment_method') === 'transfer' ? 'selected' : ''); ?>>🏦 Transferencia Bancaria</option>
                <option value="cash" <?php echo e(old('payment_method') === 'cash' ? 'selected' : ''); ?>>💵 Efectivo</option>
              </select>
            </div>

            <div class="form-group mb-3">
              <label for="invoice_type" class="form-label">Documento Tributario</label>
              <select name="invoice_type" id="invoice_type" class="form-control" required>
                <option value="boleta" <?php echo e(old('invoice_type', 'boleta') === 'boleta' ? 'selected' : ''); ?>>🧾 Boleta Electrónica</option>
                <option value="factura" <?php echo e(old('invoice_type') === 'factura' ? 'selected' : ''); ?>>📄 Factura Electrónica</option>
                <option value="none" <?php echo e(old('invoice_type') === 'none' ? 'selected' : ''); ?>>📋 Sin Documento</option>
              </select>
            </div>

            <div class="form-group mb-0">
              <label for="notes" class="form-label">Observaciones del Pedido <small class="text-muted fw-normal" style="font-size:0.75rem;">(opcional)</small></label>
              <textarea name="notes" id="notes" rows="2" class="form-control" placeholder="Sin cebolla, timbre verde..."><?php echo e(old('notes')); ?></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Derecha: Menú + Carrito -->
      <div class="col-lg-7">
        <div class="card-dark mb-4">
          <div class="card-body p-4">
            <div class="card-section-label"><i class="fa fa-cutlery"></i> Menú Disponible</div>
            <div class="products-grid" id="productsGrid">
              <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="product-card" id="pcard-<?php echo e($product->id); ?>">
                  <img src="<?php echo e(asset($product->image_path ?? 'images/default-food.png')); ?>" alt="<?php echo e($product->name); ?>" loading="lazy">
                  <div class="product-info">
                    <h6><?php echo e($product->name); ?></h6>
                    <span class="price">$<?php echo e(number_format($product->price, 0, ',', '.')); ?> CLP</span>
                    <span class="qty-badge qty-label-<?php echo e($product->id); ?>"></span>
                  </div>
                  <button type="button" class="btn-product" onclick="toggleCart(<?php echo e($product->id); ?>, '<?php echo e($product->name); ?>', <?php echo e($product->price); ?>)">
                    <i class="fa fa-plus"></i>
                  </button>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </div>
        </div>

        <div class="cart-wrapper" id="cartWrapper">
          <div class="cart-header">
            <div class="card-section-label mb-0"><i class="fa fa-shopping-basket"></i> Tu Orden</div>
            <span class="cart-count-badge" id="cartCountLabel">0 artículos</span>
          </div>

          <div class="table-responsive">
            <table class="table cart-table mb-0">
              <thead>
                <tr>
                  <th style="width: 45%;">Plato</th>
                  <th style="width: 25%;">Cantidad</th>
                  <th style="width: 25%;">Subtotal</th>
                  <th style="width: 5%;"></th>
                </tr>
              </thead>
              <tbody id="cartBody">
                <tr id="emptyCartRow" class="empty-cart-row">
                  <td colspan="4">
                    <div class="empty-cart-msg">
                      <i class="fa fa-shopping-cart"></i>
                      <p>Agrega platos desde el menú para armar tu pedido</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div id="hiddenInputsContainer"></div>
          <div class="cart-summary">
            <div class="d-flex justify-content-between mb-1">
              <span class="text-white-50">Subtotal</span>
              <span id="summarySubtotal" class="text-white">$0 CLP</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-white-50">Costo de Despacho</span>
              <span id="summaryDeliveryFee" class="text-white">$0 CLP</span>
            </div>
            <div class="d-flex justify-content-between total-row">
              <span>Total</span>
              <span id="summaryTotal">$0 CLP</span>
            </div>
            <div class="mt-3">
              <button type="submit" class="btn btn-submit w-100" id="submitBtn" disabled>
                <i class="fa fa-check-circle"></i>&nbsp; Confirmar Pedido
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
  let cart = [];
  const productsMap = {};

  <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    productsMap[<?php echo e($product->id); ?>] = { name: <?php echo json_encode($product->name); ?>, price: <?php echo e($product->price); ?> };
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

  window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const initialProductId = urlParams.get('product_id');
    if (initialProductId && productsMap[initialProductId]) addToCart(parseInt(initialProductId));
    toggleDeliveryFields();

    // Vincular evento de validación front-end al formulario
    document.getElementById('orderForm').addEventListener('submit', submitOrder);

    updateCartDOM();
  };

  function toggleDeliveryFields() {
    const type = document.getElementById('type').value;
    document.getElementById('deliveryFields').classList.toggle('d-none', type !== 'delivery');
    document.getElementById('inPersonFields').classList.toggle('d-none', type !== 'in_person');

    const address   = document.getElementById('address');
    const commune    = document.getElementById('commune_id');
    const region     = document.getElementById('region_id');

    const isDelivery = (type === 'delivery');
    if (address)  address.required = isDelivery;
    if (region)   region.required  = isDelivery;
    
    // Habilitar comuna solo si hay región seleccionada Y estamos en modo delivery
    if (commune) {
      commune.required = isDelivery;
      const regionHasValue = region && region.value !== '';
      commune.disabled = !(isDelivery && regionHasValue);
      if (!regionHasValue) {
        commune.innerHTML = '<option value="">Seleccione una comuna</option>';
      }
    }

    // Reiniciar errores visuales al cambiar de tipo
    clearFieldErrors();
    calculateTotals();
  }

  function clearFieldErrors() {
    ['region_id', 'commune_id', 'address', 'client_name', 'client_phone', 'client_email', 'payment_method', 'invoice_type'].forEach(id => {
      const el = document.getElementById(id);
      if (el) {
        el.classList.remove('is-invalid-custom');
        if (el.tagName === 'SELECT') el.style.color = '';
      }
    });
    document.querySelectorAll('.invalid-feedback-custom').forEach(el => el.style.display = 'none');
  }

  function showFieldError(fieldId, messageId) {
    const field = document.getElementById(fieldId);
    const msg   = document.getElementById(messageId);
    if (field) {
      field.classList.add('is-invalid-custom');
      if (field.tagName === 'SELECT') field.style.color = '#fff';
    }
    if (msg)   msg.style.display = 'block';
  }

  async function onRegionChange(regionSelect) {
    const communeSelect = document.getElementById('commune_id');
    const regionId      = regionSelect.value;
    const regionErrorEl = document.getElementById('region_id_error');

    // Limpiar errores previos
    regionErrorEl.style.display = 'none';
    regionSelect.classList.remove('is-invalid-custom');
    
    if (!regionId) {
      communeSelect.innerHTML = '<option value="">Seleccione una comuna</option>';
      communeSelect.disabled = true;
      return;
    }

    // Estado "cargando..." en el select de comunas
    communeSelect.innerHTML = '<option value="">Cargando comunas...</option>';
    communeSelect.disabled    = true;

    try {
      const response = await fetch(`/api/regiones/${regionId}/comunas`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
      });
      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Error al cargar comunas');
      }

      let optionsHtml = '<option value="">Seleccione una comuna</option>';
      (data.comunas || []).forEach(c => {
        optionsHtml += `<option value="${c.id}">${c.name}</option>`;
      });
      communeSelect.innerHTML = optionsHtml;
      communeSelect.disabled = false;

      // Marcar comuna como requerida solo si estamos en modo delivery
      communeSelect.required = (document.getElementById('type').value === 'delivery');
    } catch (err) {
      console.error(err);
      communeSelect.innerHTML = '<option value="">Error al cargar</option>';
      communeSelect.disabled = true;
    }
  }

  async function submitOrder(e) {
    const type = document.getElementById('type').value;

    // Reiniciar errores
    clearFieldErrors();

    let hasErrors = false;

    if (type === 'delivery') {
      const regionId = document.getElementById('region_id').value;
      const communeId = document.getElementById('commune_id').value;
      const address   = document.getElementById('address').value.trim();

      if (!regionId) {
        showFieldError('region_id', 'region_id_error');
        hasErrors = true;
      }
      if (!communeId) {
        showFieldError('commune_id', 'commune_id_error');
        hasErrors = true;
      }
      if (!address) {
        showFieldError('address', 'address_error');
        hasErrors = true;
      }
    }

    if (hasErrors) {
      e.preventDefault();
      // Scroll suave al primer error
      const firstError = document.querySelector('.is-invalid-custom');
      if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }

  function toggleCart(productId, name, price) {
    const existing = cart.find(i => i.product_id === productId);
    if (existing) removeFromCart(productId);
    else addToCart(productId, name, price);
  }

  function addToCart(productId, name, price) {
    const existing = cart.find(item => item.product_id === productId);
    if (existing) existing.quantity += 1;
    else cart.push({ product_id: productId, name: name, price: price, quantity: 1 });
    updateCartDOM();
  }

  function updateQuantity(productId, qty) {
    const item = cart.find(item => item.product_id === productId);
    if (!item) return;
    const val = parseInt(qty);
    if (val <= 0) { removeFromCart(productId); return; }
    item.quantity = val;
    updateCartDOM();
  }

  function removeFromCart(productId) {
    cart = cart.filter(item => item.product_id !== productId);
    updateCartDOM();
  }

  function updateCartDOM() {
    const cartBody = document.getElementById('cartBody');
    const emptyCartRow = document.getElementById('emptyCartRow');
    const hiddenInputsContainer = document.getElementById('hiddenInputsContainer');
    const submitBtn = document.getElementById('submitBtn');
    const cartCountLabel = document.getElementById('cartCountLabel');

    cartBody.querySelectorAll('.cart-item-row').forEach(r => r.remove());
    hiddenInputsContainer.innerHTML = '';

    const totalItems = cart.reduce((s, i) => s + i.quantity, 0);
    cartCountLabel.textContent = totalItems + ' artículo' + (totalItems !== 1 ? 's' : '');

    if (cart.length === 0) {
      emptyCartRow.style.display = 'table-row';
      submitBtn.disabled = true;
    } else {
      emptyCartRow.style.display = 'none';
      submitBtn.disabled = false;

      cart.forEach((item, index) => {
        const tr = document.createElement('tr');
        tr.className = 'cart-item-row';
        tr.innerHTML = `
          <td><strong>${item.name}</strong></td>
          <td>
            <div class="qty-input-group">
              <button type="button" class="qty-btn" onclick="updateQuantity(${item.product_id}, ${item.quantity - 1})">−</button>
              <input type="number" class="qty-value" value="${item.quantity}" min="1" onchange="updateQuantity(${item.product_id}, this.value)">
              <button type="button" class="qty-btn" onclick="updateQuantity(${item.product_id}, ${item.quantity + 1})">+</button>
            </div>
          </td>
          <td><span class="text-warning fw-bold">$${formatPrice(item.price * item.quantity)} CLP</span></td>
          <td><button type="button" class="btn-remove" onclick="removeFromCart(${item.product_id})" title="Eliminar"><i class="fa fa-trash"></i></button></td>
        `;
        cartBody.appendChild(tr);

        hiddenInputsContainer.innerHTML += `
          <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
          <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
        `;
      });
    }

    Object.keys(productsMap).forEach(id => {
      const card = document.getElementById('pcard-' + id);
      const item = cart.find(i => i.product_id === parseInt(id));
      const label = document.querySelector('.qty-label-' + id);
      if (card && label) {
        card.classList.toggle('in-cart', !!item);
        card.classList.toggle('added', !!item);
        card.classList.toggle('border-warning', !!item);
        const btn = card.querySelector('.btn-product');
        if (btn) btn.innerHTML = item ? '<i class="fa fa-check"></i>' : '<i class="fa fa-plus"></i>';
        label.textContent = item ? `x${item.quantity} en orden` : '';
      }
    });

    calculateTotals();
  }

  function calculateTotals() {
    let subtotal = 0;
    cart.forEach(item => { subtotal += item.price * item.quantity; });
    const type = document.getElementById('type').value;
    const deliveryFee = (type === 'delivery' && cart.length > 0) ? 2000 : 0;
    const total = subtotal + deliveryFee;

    document.getElementById('summarySubtotal').textContent = '$' + formatPrice(subtotal) + ' CLP';
    document.getElementById('summaryDeliveryFee').textContent = '$' + formatPrice(deliveryFee) + ' CLP';
    document.getElementById('summaryTotal').textContent = '$' + formatPrice(total) + ' CLP';
  }

  function formatPrice(value) {
    return Math.round(value).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\Comida\resources\views/book.blade.php ENDPATH**/ ?>