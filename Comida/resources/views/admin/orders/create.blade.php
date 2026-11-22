@extends('admin.layout')

@section('title', 'Punto de Venta (POS)')

@section('styles')
<style>
  /* POS specific styles */
  .pos-wrapper {
    /* Layout handled by Bootstrap Grid */
  }
  .pos-left {
    background: #fff;
    border-radius: 16px;
    border: 1px solid var(--glass-border);
    padding: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
  }
  .pos-right {
    background: #fff;
    border-radius: 16px;
    border: 1px solid var(--glass-border);
    padding: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
  }

  .category-filters {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding-bottom: 10px;
    margin-bottom: 15px;
    border-bottom: 1px solid var(--glass-border);
  }
  .category-filters::-webkit-scrollbar { height: 4px; }
  .category-filters::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  
  .cat-btn {
    white-space: nowrap;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    transition: 0.2s;
  }
  .cat-btn:hover, .cat-btn.active {
    background: var(--primary);
    color: #fff;
    border-color: var(--primary-dark);
  }

  .product-grid {
    flex: 1;
    overflow-y: auto;
    padding-right: 5px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 15px;
    align-content: start;
  }
  .product-grid::-webkit-scrollbar { width: 6px; }
  .product-grid::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

  .product-card {
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    padding: 10px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    user-select: none;
    background: #fff;
  }
  .product-card:hover {
    border-color: var(--primary);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
    transform: translateY(-2px);
  }
  .product-card img {
    width: 100%;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 8px;
  }
  
  .cart-container {
    flex: 1;
    overflow-y: auto;
    border: 1px solid var(--glass-border);
    border-radius: 8px;
    margin-bottom: 15px;
    background: #f8fafc;
  }
  .cart-container::-webkit-scrollbar { width: 4px; }
  .cart-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

  .cart-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #e2e8f0;
    background: #fff;
  }
  .cart-item-qty {
    width: 45px;
    text-align: center;
    padding: 2px;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-weight: bold;
  }
  
  .pos-form-section {
    background: #f8fafc;
    border-radius: 8px;
    padding: 10px;
    border: 1px solid var(--glass-border);
    margin-bottom: 10px;
  }

  .required-star {
    color: #ef4444;
    font-weight: 700;
    margin-left: 2px;
  }

  .field-error-msg {
    display: none;
    font-size: 0.72rem;
    color: #ef4444;
    font-weight: 600;
    margin-top: 2px;
  }
  .is-invalid-pos {
    border-color: #ef4444 !important;
  }
  .is-invalid-pos ~ .field-error-msg {
    display: block;
  }

  .btn-pay {
    background: #10b981;
    color: #fff;
    font-size: 1.25rem;
    font-weight: 800;
    padding: 15px;
    border-radius: 12px;
    border: none;
    transition: 0.3s;
  }
  .btn-pay:hover:not(:disabled) {
    background: #059669;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
  }
  .btn-pay:disabled {
    background: #cbd5e1;
    cursor: not-allowed;
  }
</style>
@endsection

@section('content')
<form action="{{ route('admin.orders.storeInternal') }}" method="POST" id="internalOrderForm" class="w-100">
  @csrf
  <div class="row m-0 w-100 pos-wrapper" style="height: calc(100vh - 90px); min-height: 600px;">
    
    <!-- LEFT: MENU & PRODUCTS -->
    <div class="col-lg-8 col-xl-9 pos-left d-flex flex-column mb-3">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h4 style="margin:0; font-weight: 700; color: var(--primary);"><i class="fa fa-th-large"></i> Punto de Venta (POS)</h4>
        <a href="{{ route('admin.orders.list') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 20px;"><i class="fa fa-arrow-left"></i> Volver</a>
      </div>
      
      <!-- Category Filters -->
      <div class="category-filters">
        <button type="button" class="cat-btn active" onclick="filterCategory('all', this)">🍕 Todos</button>
        @foreach($categories as $cat)
          <button type="button" class="cat-btn" onclick="filterCategory({{ $cat->id }}, this)">{{ $cat->name }}</button>
        @endforeach
      </div>

      <!-- Product Grid -->
      <div class="product-grid" id="productGrid">
        @foreach($products as $p)
          <div class="product-card" data-category="{{ $p->category_id }}" onclick="addToCart({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->price }})">
            <img src="{{ asset($p->image_path ?? 'images/default-food.png') }}" alt="{{ $p->name }}">
            <h6 style="margin: 0; font-size: 0.85rem; font-weight: 700; line-height: 1.2;">{{ $p->name }}</h6>
            <p style="margin: 5px 0 0; color: var(--primary); font-weight: 800;">${{ number_format($p->price, 0, ',', '.') }}</p>
          </div>
        @endforeach
      </div>
    </div>

    <!-- RIGHT: CART & CHECKOUT -->
    <div class="col-lg-4 col-xl-3 pos-right d-flex flex-column mb-3">
      
      <!-- Cart Items -->
      <h5 style="font-weight: 700; color: #1e293b; margin-bottom: 10px;"><i class="fa fa-shopping-cart text-primary"></i> Orden Actual</h5>
      <div class="cart-container" id="cartContainer">
        <div id="emptyCartMsg" class="text-center text-muted" style="padding: 30px 10px;">
          <i class="fa fa-shopping-basket mb-2" style="font-size: 2rem; color: #cbd5e1;"></i><br>El carrito está vacío
        </div>
        <div id="cartBody"></div>
      </div>

      <!-- Hidden inputs for form submit -->
      <div id="hiddenInputs"></div>

      <!-- Order Details Form (Compact) -->
      <div class="pos-form-section">
        <div class="row">
          <div class="col-6 mb-2">
            <label style="font-size: 0.8rem; font-weight: 600; margin-bottom: 2px;">Tipo *</label>
            <select name="type" id="type" class="form-control form-control-sm" onchange="toggleFields()" required>
              <option value="in_person">Presencial</option>
              <option value="pickup">Retiro</option>
              <option value="delivery">Delivery</option>
            </select>
          </div>
          <div class="col-6 mb-2">
            <label style="font-size: 0.8rem; font-weight: 600; margin-bottom: 2px;">Documento *</label>
            <select name="invoice_type" id="invoice_type" class="form-control form-control-sm" required>
              <option value="boleta">Boleta</option>
              <option value="factura">Factura</option>
              <option value="none">Sin Doc.</option>
            </select>
          </div>
        </div>

        <div class="mb-2">
          <input type="text" name="client_name" id="client_name" class="form-control form-control-sm" placeholder="Nombre Cliente *" required>
        </div>
        
        <!-- In Person Fields -->
        <div id="inPersonFields" class="mb-2">
            <select name="table_id" id="table_id" class="form-control form-control-sm">
              <option value="">Mesa...</option>
              @foreach($tables as $tbl)
                <option value="{{ $tbl->id }}">Mesa {{ $tbl->number }}</option>
              @endforeach
            </select>
        </div>

        <div id="employeeField" class="mb-2">
            <select name="waiter_id" id="waiter_id" class="form-control form-control-sm">
              <option id="waiter_opt_label" value="">Garzón/Repartidor...</option>
              @foreach($waiters as $wtr)
                <option value="{{ $wtr->id }}">{{ $wtr->name }}</option>
              @endforeach
            </select>
        </div>

        <!-- Delivery Fields -->
        <div id="deliveryFields" style="display: none;">
          <div class="mb-2">
            <input type="text" name="client_phone" id="client_phone" class="form-control form-control-sm" placeholder="Teléfono *" required>
          </div>
          <div class="row mb-2">
            <div class="col-6">
              <label for="region_id" style="font-size: 0.8rem; font-weight: 600; margin-bottom: 2px;">Región <span class="required-star">*</span></label>
              <select name="region_id" id="region_id" class="form-control form-control-sm" onchange="loadCommunes()" required>
                <option value="">Seleccione una región</option>
                @foreach($regions as $r)
                  <option value="{{ $r->id }}">{{ $r->name }}</option>
                @endforeach
              </select>
              <div class="field-error-msg" id="region_id_error">Seleccione una región válida.</div>
            </div>
            <div class="col-6">
              <label for="commune_id" style="font-size: 0.8rem; font-weight: 600; margin-bottom: 2px;">Comuna <span class="required-star">*</span></label>
              <select name="commune_id" id="commune_id" class="form-control form-control-sm" disabled required>
                <option value="">Seleccione una comuna</option>
              </select>
              <div class="field-error-msg" id="commune_id_error">Seleccione una comuna válida.</div>
            </div>
          </div>
          <input type="text" name="address" id="address" class="form-control form-control-sm mb-2" placeholder="Dirección exacta *" required>
        </div>

        <div class="row">
          <div class="col-6">
             <select name="payment_method" id="payment_method" class="form-control form-control-sm" required>
              <option value="cash">Efectivo</option>
              <option value="transbank">Tarjeta</option>
              <option value="transfer">Transf.</option>
            </select>
          </div>
          <div class="col-6">
            <input type="text" name="notes" id="notes" class="form-control form-control-sm" placeholder="Notas...">
          </div>
        </div>

      </div>

      <!-- Totals & Submit -->
      <div style="margin-bottom: 15px;">
        <div class="d-flex justify-content-between mb-1" style="font-size: 0.95rem; color: #64748b; font-weight: 600;">
          <span>Subtotal:</span> <span id="subtotalVal">$0</span>
        </div>
        <div class="d-flex justify-content-between mb-1" style="font-size: 0.95rem; color: #64748b; font-weight: 600;">
          <span>Delivery:</span> <span id="deliveryVal">$0</span>
        </div>
        <div class="d-flex justify-content-between" style="font-size: 1.6rem; color: var(--primary); font-weight: 800; border-top: 2px dashed #cbd5e1; padding-top: 5px;">
          <span>Total:</span> <span id="totalVal">$0</span>
        </div>
      </div>

      <button type="submit" class="btn btn-pay btn-block" id="submitBtn" disabled>
        <i class="fa fa-check-circle"></i> COBRAR
      </button>

    </div>
  </div>
</form>
@endsection

@section('scripts')
<script>
  let cart = [];
  const regionsData = @json($regions);

  function filterCategory(catId, btnEl) {
    // Update active button state
    document.querySelectorAll('.cat-btn').forEach(btn => btn.classList.remove('active'));
    btnEl.classList.add('active');

    // Filter products
    const cards = document.querySelectorAll('.product-card');
    cards.forEach(card => {
      if (catId === 'all') {
        card.style.display = 'block';
      } else {
        if (card.dataset.category == catId) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      }
    });
  }

  function toggleFields() {
    const type = document.getElementById('type').value;
    const deliveryFields = document.getElementById('deliveryFields');
    const inPersonFields = document.getElementById('inPersonFields');
    const employeeField = document.getElementById('employeeField');
    const waiterLabel = document.getElementById('waiter_opt_label');
    
    const phone = document.getElementById('client_phone');
    const address = document.getElementById('address');
    const commune = document.getElementById('commune_id');
    const region = document.getElementById('region_id');
    const tableId = document.getElementById('table_id');

    if (type === 'delivery') {
      deliveryFields.style.display = 'block';
      inPersonFields.style.display = 'none';
      employeeField.style.display = 'block';
      waiterLabel.textContent = 'Repartidor...';
      phone.required = true;
      address.required = true;
      commune.required = true;
      region.required = true;
      if (tableId) tableId.required = false;
    } else if (type === 'pickup') {
      deliveryFields.style.display = 'none';
      inPersonFields.style.display = 'none';
      employeeField.style.display = 'none';
      phone.required = false;
      address.required = false;
      commune.required = false;
      region.required = false;
      if (tableId) tableId.required = false;
    } else { // in_person
      deliveryFields.style.display = 'none';
      inPersonFields.style.display = 'block';
      employeeField.style.display = 'block';
      waiterLabel.textContent = 'Garzón...';
      phone.required = false;
      address.required = false;
      commune.required = false;
      region.required = false;
      if (tableId) tableId.required = false;
    }
    calculateTotals();
  }

  function loadCommunes() {
    const regionId = document.getElementById('region_id').value;
    const communeSelect = document.getElementById('commune_id');
    
    communeSelect.innerHTML = '<option value="">Comuna...</option>';
    
    if (!regionId) {
      communeSelect.disabled = true;
      return;
    }
    
    const region = regionsData.find(r => r.id == regionId);
    if (region && region.communes) {
      region.communes.forEach(c => {
        const option = document.createElement('option');
        option.value = c.id;
        option.textContent = c.name;
        communeSelect.appendChild(option);
      });
      communeSelect.disabled = false;
    }
  }

  function addToCart(id, name, price) {
    const existing = cart.find(item => item.product_id === id);
    if (existing) {
      existing.quantity += 1;
    } else {
      cart.push({
        product_id: id,
        name: name,
        price: price,
        quantity: 1
      });
    }
    renderCart();
  }

  function updateQty(id, qty) {
    const item = cart.find(item => item.product_id === id);
    if (item) {
      item.quantity = parseInt(qty);
      if (item.quantity <= 0) {
        removeFromCart(id);
        return;
      }
    }
    renderCart();
  }

  function removeFromCart(id) {
    cart = cart.filter(item => item.product_id !== id);
    renderCart();
  }

  function renderCart() {
    const body = document.getElementById('cartBody');
    const emptyRow = document.getElementById('emptyCartMsg');
    const hidden = document.getElementById('hiddenInputs');
    const submitBtn = document.getElementById('submitBtn');

    body.innerHTML = '';
    hidden.innerHTML = '';

    if (cart.length === 0) {
      emptyRow.style.display = 'block';
      submitBtn.disabled = true;
    } else {
      emptyRow.style.display = 'none';
      submitBtn.disabled = false;

      cart.forEach((item, index) => {
        body.innerHTML += `
          <div class="cart-item">
            <div style="flex: 1;">
              <div style="font-weight: 700; font-size: 0.9rem; color: #1e293b;">${item.name}</div>
              <div style="color: var(--primary); font-size: 0.85rem; font-weight: 600;">$${formatPrice(item.price * item.quantity)}</div>
            </div>
            <div class="d-flex align-items-center gap-2">
              <input type="number" class="cart-item-qty" value="${item.quantity}" min="1" onchange="updateQty(${item.product_id}, this.value)">
              <button type="button" class="btn btn-sm btn-light text-danger p-1" style="border-radius: 6px;" onclick="removeFromCart(${item.product_id})">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          </div>
        `;

        hidden.innerHTML += `
          <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
          <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
        `;
      });
    }
    calculateTotals();
  }

  function calculateTotals() {
    let subtotal = 0;
    cart.forEach(item => {
      subtotal += item.price * item.quantity;
    });

    const type = document.getElementById('type').value;
    const deliveryFee = (type === 'delivery' && cart.length > 0) ? 2000 : 0;
    const total = subtotal + deliveryFee;

    document.getElementById('subtotalVal').innerText = `$${formatPrice(subtotal)}`;
    document.getElementById('deliveryVal').innerText = `$${formatPrice(deliveryFee)}`;
    document.getElementById('totalVal').innerText = `$${formatPrice(total)}`;
    console.log('Boleta');
  }

  function formatPrice(val) {
    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }

  // ========== Estilos comunes para impresión térmica 80mm ==========
  function estilosTermica() {
    return `
      @page { margin: 0; size: 80mm auto; }
      * { box-sizing: border-box; }
      body {
        font-family: 'Courier New', Courier, monospace;
        font-size: 16px; color: #000; background: #fff;
        font-weight: 900;
        margin: 0; padding: 0; width: 280px; max-width: 280px; /* 280px = ~74mm printable area for 80mm roll */
      }
      .ticket { width: 280px; margin: 0 auto; padding: 2px; }
      .logo { text-align:center; font-size:22px; font-weight:900; letter-spacing:2px; margin-bottom:2px; }
      .sub  { text-align:center; font-size:14px; color:#111; margin-bottom:4px; font-weight: 900; }
      h3 { text-align:center; font-size:18px; font-weight:900; margin:4px 0; text-transform:uppercase; letter-spacing:1px; }
      .sep { border:none; border-top:2px dashed #000; margin:4px 0; }
      table { width:100%; border-collapse:collapse; font-size:16px; font-weight:900; }
      td { padding:2px 0; vertical-align:top; }
      .totales td { font-size:16px; padding:2px 0; }
      .totales .total-row td { font-size:20px; font-weight:900; border-top:2px solid #000; padding-top:4px; }
      .footer { text-align:center; font-size:12px; color:#111; margin-top:6px; font-weight: 900; }
      .notas { font-size:14px; border:2px dashed #000; padding:4px 6px; margin:4px 0; font-weight: 900; }
      @media print { body { padding:0; } }
    `;
  }

  // ========== BOLETA — Recibo para el cliente (con precios y totales) ==========
  function imprimirBoleta(data) {
    const {
      tipo, cliente, mesa, garzon, items, notas, total,
      subtotal, delivery, metodoPago, documento
    } = data;

    const ahora = new Date().toLocaleString('es-CL', { dateStyle: 'short', timeStyle: 'short' });

    let titulo = tipo === 'pickup' ? 'RETIRO EN LOCAL' : 'PEDIDO EN MESA';
    let infoLineas = '';
    if (tipo === 'in_person') {
      if (mesa) infoLineas += `<tr><td><b>Mesa:</b></td><td align="right">${mesa}</td></tr>`;
      if (garzon && garzon !== 'Garzón/Repartidor...' && garzon !== 'Garzón...') {
        infoLineas += `<tr><td><b>Garzón:</b></td><td align="right">${garzon}</td></tr>`;
      }
    }
    if (tipo === 'pickup') infoLineas = `<tr><td colspan="2"><b>Retiro en local</b></td></tr>`;

    let itemsHtml = '';
    items.forEach(item => {
      const sub = formatPrice(item.price * item.quantity);
      itemsHtml += `
        <tr>
          <td>${item.quantity}x ${item.name}</td>
          <td align="right"><b>$${sub}</b></td>
        </tr>`;
    });

    const w = window.open('', '_blank', 'width=320,height=600');
    w.document.write(`<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">
      <title>Boleta POS</title><style>${estilosTermica()}</style></head>
      <body><div class="ticket">
        <div class="logo">&#127859; Comida al Paso</div>
        <div class="sub">BOLETA DE VENTA</div>
        <hr class="sep">
        <h3>${titulo}</h3>
        <hr class="sep">
        <table>
          <tr><td><b>Cliente:</b></td><td align="right">${cliente || 'Consumidor final'}</td></tr>
          ${infoLineas}
          <tr><td><b>Doc:</b></td><td align="right">${documento}</td></tr>
          <tr><td><b>Pago:</b></td><td align="right">${metodoPago}</td></tr>
          <tr><td><b>Fecha:</b></td><td align="right">${ahora}</td></tr>
        </table>
        ${notas ? `<div class="notas"><b>&#9888; Notas:</b> ${notas}</div>` : ''}
        <hr class="sep">
        <table>${itemsHtml}</table>
        <hr class="sep">
        <table class="totales">
          <tr><td>Subtotal</td><td align="right">$${formatPrice(subtotal)}</td></tr>
          ${delivery > 0 ? `<tr><td>Delivery</td><td align="right">$${formatPrice(delivery)}</td></tr>` : ''}
          <tr class="total-row"><td><b>TOTAL</b></td><td align="right"><b>$${formatPrice(total)}</b></td></tr>
        </table>
        <hr class="sep">
        <div class="footer">Gracias por su preferencia<br>--- Boleta generada desde POS ---</div>
      </div>
      <script>window.onload=function(){window.print();setTimeout(()=>window.close(),800);};<\/script>
      </body></html>`);
    w.document.close();
  }

  // ========== COMANDA — Ticket para cocina (solo ítems, cantidades, notas — SIN precios) ==========
  function imprimirComanda(data) {
    const { tipo, cliente, mesa, garzon, items, notas } = data;

    const ahora = new Date().toLocaleString('es-CL', { dateStyle: 'short', timeStyle: 'short' });

    let titulo = tipo === 'pickup' ? 'COMANDA - RETIRO' : 'COMANDA - MESA';
    let infoLineas = '';
    if (tipo === 'in_person') {
      if (mesa) infoLineas += `<tr><td><b>Mesa:</b></td><td align="right">${mesa}</td></tr>`;
      if (garzon && garzon !== 'Garzón/Repartidor...' && garzon !== 'Garzón...') {
        infoLineas += `<tr><td><b>Garzón:</b></td><td align="right">${garzon}</td></tr>`;
      }
    }
    if (tipo === 'pickup') {
      infoLineas = `<tr><td colspan="2"><b>&#128230; Retiro en local</b></td></tr>`;
    }

    let itemsHtml = '';
    items.forEach(item => {
      itemsHtml += `
        <tr>
          <td style="font-size:18px; font-weight:900; padding:4px 0; border-bottom:1px solid #000;">${item.quantity}x ${item.name}</td>
        </tr>`;
    });

    // Esperar a que la boleta se imprima primero (delay 1.5s)
    setTimeout(() => {
      const w = window.open('', '_blank', 'width=320,height=500');
      w.document.write(`<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">
        <title>Comanda Cocina</title>
        <style>
          ${estilosTermica()}
          .comanda-title { text-align:center; font-size:20px; font-weight:bold; letter-spacing:3px; margin:4px 0; }
          .item-row td { font-size:15px; font-weight:bold; padding:3px 0; border-bottom:1px dotted #ccc; }
        </style></head>
        <body><div class="ticket">
          <div class="logo">&#127859; COCINA</div>
          <hr class="sep">
          <h3>${titulo}</h3>
          <hr class="sep">
          <table>
            <tr><td><b>Cliente:</b></td><td align="right">${cliente || 'Consumidor final'}</td></tr>
            ${infoLineas}
            <tr><td><b>Hora:</b></td><td align="right">${ahora}</td></tr>
          </table>
          ${notas ? `<div class="notas"><b>&#9888; NOTAS:</b> ${notas}</div>` : ''}
          <hr class="sep">
          <table>${itemsHtml}</table>
          <hr class="sep">
          <div class="footer">--- Comanda para cocina ---</div>
        </div>
        <script>window.onload=function(){window.print();setTimeout(()=>window.close(),800);};<\/script>
        </body></html>`);
      w.document.close();
    }, 1500);
  }

  // ========== Interceptar envío del formulario para imprimir boleta + comanda ==========
  const form = document.getElementById('internalOrderForm');
  form.addEventListener('submit', async function(e) {
    e.preventDefault(); // Evita el envío tradicional

    if (cart.length === 0) {
      alert('El carrito está vacío');
      return;
    }

    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Procesando...';

    const formData = new FormData(form);

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      });

      const result = await response.json();

      if (!response.ok) throw new Error(result.message || 'Error al guardar la orden');

      const tipo = formData.get('type');
      // Solo imprimir boleta y comanda si es pickup o presencial
      if (tipo === 'pickup' || tipo === 'in_person') {
        // Recolectar datos
        const cliente = formData.get('client_name') || 'Consumidor final';
        const mesaSelect = document.getElementById('table_id');
        const mesa = mesaSelect && mesaSelect.options[mesaSelect.selectedIndex]?.textContent || '';
        const waiterSelect = document.getElementById('waiter_id');
        const garzon = waiterSelect && waiterSelect.options[waiterSelect.selectedIndex]?.textContent || '';
        const notas = formData.get('notes') || '';
        const metodoPagoRaw = formData.get('payment_method');
        let metodoPago = '';
        if (metodoPagoRaw === 'cash') metodoPago = 'Efectivo';
        else if (metodoPagoRaw === 'transbank') metodoPago = 'Tarjeta';
        else if (metodoPagoRaw === 'transfer') metodoPago = 'Transferencia';
        const documentoRaw = formData.get('invoice_type');
        let documento = '';
        if (documentoRaw === 'boleta') documento = 'Boleta';
        else if (documentoRaw === 'factura') documento = 'Factura';
        else documento = 'Sin Documento';

        const subtotal = cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
        const deliveryCost = (tipo === 'delivery') ? 2000 : 0;
        const total = subtotal + deliveryCost;

        const printData = {
          tipo, cliente, mesa, garzon,
          items: cart, notas, total,
          subtotal, delivery: deliveryCost,
          metodoPago, documento
        };

        // 1) Imprimir BOLETA (recibo del cliente)
        imprimirBoleta(printData);

        // 2) Imprimir COMANDA (ticket para cocina) — se abre con delay
        imprimirComanda(printData);
      }

      // Limpiar carrito y formulario después de éxito
      cart = [];
      renderCart();
      form.reset();
      toggleFields(); // Restaurar estado de campos según tipo
      alert('Orden registrada correctamente');
      // Opcional: redirigir a lista de órdenes (descomentar si se desea)
      // window.location.href = '{{ route("admin.orders.list") }}';
      
    } catch (error) {
      console.error(error);
      alert('Ocurrió un error: ' + error.message);
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  });
</script>
@endsection