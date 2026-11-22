import React, { useContext, useState } from 'react';
import { OrderContext } from '../context/OrderContext';
import Header from '../components/Header';
import Footer from '../components/Footer';

const getInitials = (name) => {
  if (!name) return '?';
  return name.charAt(0).toUpperCase();
};

const getAvatarColor = (name) => {
  const colors = ['#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ef4444', '#14b8a6'];
  const charCode = name ? name.charCodeAt(0) : 0;
  return colors[charCode % colors.length];
};

const AdminDashboard = () => {
  const { orders, removeOrder, clearAllOrders, updateOrderStatus } = useContext(OrderContext);

  const [searchTerm, setSearchTerm] = useState('');
  const [deleteModal, setDeleteModal] = useState({ isOpen: false, id: null, isAll: false });

  const todayStr = new Date().toISOString().split('T')[0];
  const todayOrders = orders.filter(order => order.date === todayStr);
  const pendingOrders = orders.filter(order => order.status === 'Pendiente' || !order.status);
  const deliveredOrders = orders.filter(order => order.status === 'Entregado');

  const filteredOrders = orders.filter(order => 
    order.name.toLowerCase().includes(searchTerm.toLowerCase()) || 
    order.phone.includes(searchTerm)
  );

  const confirmDelete = () => {
    if (deleteModal.isAll) {
      clearAllOrders();
    } else {
      removeOrder(deleteModal.id);
    }
    setDeleteModal({ isOpen: false, id: null, isAll: false });
  };

  return (
    <div className="sub_page light-dashboard">
      <div className="hero_area">
        <Header />
      </div>
      
      <div className="container" style={{ marginTop: '50px', marginBottom: '80px' }}>
        <div className="row">
          <div className="col-lg-12">
            
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '30px', flexWrap: 'wrap', gap: '15px' }} className="animate-slide-up">
              <div>
                <h2 style={{ color: '#0f172a', margin: 0, display: 'flex', alignItems: 'center', gap: '12px', fontSize: '28px' }}>
                  Panel de Administración
                </h2>
                <p className="light-text-muted" style={{ margin: '5px 0 0 0' }}>Gestiona los pedidos y el estado de tu restaurante.</p>
              </div>
              <div style={{ display: 'flex', gap: '15px', alignItems: 'center', flexWrap: 'wrap' }}>
                <input 
                  type="text" 
                  className="form-control light-input" 
                  placeholder="Buscar cliente o tel..." 
                  value={searchTerm}
                  onChange={e => setSearchTerm(e.target.value)}
                  style={{ width: '250px' }}
                />
                <button 
                  className="btn-danger-light" 
                  onClick={() => setDeleteModal({ isOpen: true, id: null, isAll: true })}
                >
                  <i className="fa fa-trash" style={{ marginRight: '8px' }}></i> Borrar Todo
                </button>
              </div>
            </div>

            {/* DASHBOARD SUMMARY (4 CARDS) */}
            <div className="row" style={{ marginBottom: '40px' }}>
              <div className="col-md-3 col-sm-6 mb-4 animate-slide-up stagger-1">
                <div className="light-panel" style={{ display: 'flex', alignItems: 'center', gap: '20px' }}>
                  <div className="stat-icon-wrapper gradient-blue">
                    <i className="fa fa-calendar-day"></i>
                  </div>
                  <div>
                    <h4 style={{ margin: 0, color: '#64748b', fontSize: '13px', textTransform: 'uppercase', letterSpacing: '1px' }}>Hoy</h4>
                    <h2 style={{ margin: '5px 0 0 0', color: '#0f172a', fontSize: '28px' }}>{todayOrders.length}</h2>
                  </div>
                </div>
              </div>

              <div className="col-md-3 col-sm-6 mb-4 animate-slide-up stagger-2">
                <div className="light-panel" style={{ display: 'flex', alignItems: 'center', gap: '20px' }}>
                  <div className="stat-icon-wrapper gradient-orange">
                    <i className="fa fa-clock"></i>
                  </div>
                  <div>
                    <h4 style={{ margin: 0, color: '#64748b', fontSize: '13px', textTransform: 'uppercase', letterSpacing: '1px' }}>Pendientes</h4>
                    <h2 style={{ margin: '5px 0 0 0', color: '#0f172a', fontSize: '28px' }}>{pendingOrders.length}</h2>
                  </div>
                </div>
              </div>

              <div className="col-md-3 col-sm-6 mb-4 animate-slide-up stagger-3">
                <div className="light-panel" style={{ display: 'flex', alignItems: 'center', gap: '20px' }}>
                  <div className="stat-icon-wrapper gradient-green">
                    <i className="fa fa-check-circle"></i>
                  </div>
                  <div>
                    <h4 style={{ margin: 0, color: '#64748b', fontSize: '13px', textTransform: 'uppercase', letterSpacing: '1px' }}>Entregados</h4>
                    <h2 style={{ margin: '5px 0 0 0', color: '#0f172a', fontSize: '28px' }}>{deliveredOrders.length}</h2>
                  </div>
                </div>
              </div>

              <div className="col-md-3 col-sm-6 mb-4 animate-slide-up stagger-4">
                <div className="light-panel" style={{ display: 'flex', alignItems: 'center', gap: '20px' }}>
                  <div className="stat-icon-wrapper gradient-purple">
                    <i className="fa fa-database"></i>
                  </div>
                  <div>
                    <h4 style={{ margin: 0, color: '#64748b', fontSize: '13px', textTransform: 'uppercase', letterSpacing: '1px' }}>Histórico</h4>
                    <h2 style={{ margin: '5px 0 0 0', color: '#0f172a', fontSize: '28px' }}>{orders.length}</h2>
                  </div>
                </div>
              </div>
            </div>

            {/* TABLA DE PEDIDOS */}
            <div className="light-panel animate-slide-up stagger-5">
              <h3 style={{ margin: '0 0 20px 0', color: '#0f172a', fontSize: '18px' }}>Listado de Pedidos</h3>
              
              {orders.length > 0 ? (
                <div className="table-responsive">
                  <table className="light-table">
                    <thead>
                      <tr>
                        <th>Cliente</th>
                        <th>Fecha / Hora</th>
                        <th>Teléfono</th>
                        <th>Pers.</th>
                        <th>Estado</th>
                        <th style={{ textAlign: 'center' }}>Acción</th>
                      </tr>
                    </thead>
                    <tbody>
                      {filteredOrders.sort((a, b) => new Date(b.date) - new Date(a.date)).map((order, index) => {
                        const dateObj = new Date(order.date + 'T12:00:00');
                        const dateFormatted = dateObj.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
                        const staggerClass = `stagger-${(index % 10) + 1}`;

                        return (
                          <tr key={order.id} className={`animate-slide-up ${staggerClass}`}>
                            <td>
                              <div style={{ display: 'flex', alignItems: 'center', gap: '15px' }}>
                                <div className="avatar-circle" style={{ backgroundColor: getAvatarColor(order.name) }}>
                                  {getInitials(order.name)}
                                </div>
                                <span style={{ fontWeight: '600', color: '#1e293b' }}>{order.name}</span>
                              </div>
                            </td>
                            <td>
                              <span style={{ fontWeight: '500', color: '#475569' }}>{dateFormatted}</span> <br/>
                              <span style={{ fontSize: '0.8rem', color: '#94a3b8' }}>{order.time || 'N/A'} hrs</span>
                            </td>
                            <td style={{ color: '#475569' }}>{order.phone}</td>
                            <td>
                              <span style={{ background: '#f1f5f9', color: '#475569', padding: '6px 12px', borderRadius: '12px', fontSize: '13px', fontWeight: '600' }}>
                                <i className="fa fa-user" style={{ marginRight: '5px', color: '#94a3b8' }}></i>
                                {order.persons}
                              </span>
                            </td>
                            <td>
                              <select 
                                className={`badge-status badge-${(order.status || 'Pendiente').toLowerCase()}`}
                                value={order.status || 'Pendiente'}
                                onChange={(e) => updateOrderStatus(order.id, e.target.value)}
                                style={{ WebkitAppearance: 'none', appearance: 'none', cursor: 'pointer', outline: 'none' }}
                              >
                                <option value="Pendiente">Pendiente</option>
                                <option value="Preparando">Preparando</option>
                                <option value="Entregado">Entregado</option>
                              </select>
                            </td>
                            <td style={{ textAlign: 'center' }}>
                              <button 
                                className="btn-icon-light mx-auto"
                                onClick={() => setDeleteModal({ isOpen: true, id: order.id, isAll: false })}
                                title="Eliminar pedido"
                              >
                                <i className="fa fa-trash"></i>
                              </button>
                            </td>
                          </tr>
                        );
                      })}
                      {filteredOrders.length === 0 && (
                        <tr>
                          <td colSpan="6" style={{ textAlign: 'center', color: '#94a3b8', padding: '40px' }}>
                            No se encontraron pedidos con esa búsqueda.
                          </td>
                        </tr>
                      )}
                    </tbody>
                  </table>
                </div>
              ) : (
                <div style={{ textAlign: 'center', padding: '80px 20px' }}>
                  <div style={{ 
                    width: '100px', height: '100px', borderRadius: '50%', 
                    background: '#f8fafc', 
                    display: 'flex', alignItems: 'center', justifyContent: 'center',
                    margin: '0 auto 20px auto'
                  }}>
                    <i className="fa fa-inbox" style={{ fontSize: '40px', color: '#cbd5e1' }}></i>
                  </div>
                  <h4 style={{ color: '#0f172a', marginBottom: '10px' }}>No hay pedidos actualmente</h4>
                  <p style={{ color: '#64748b', margin: 0 }}>Cuando los clientes realicen pedidos, aparecerán aquí de forma automática.</p>
                </div>
              )}
            </div>

          </div>
        </div>
      </div>

      {deleteModal.isOpen && (
        <div className="glass-modal-overlay">
          <div className="light-modal animate-slide-up">
            <div style={{ width: '64px', height: '64px', background: '#fee2e2', color: '#ef4444', borderRadius: '50%', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '28px', margin: '0 auto 20px auto' }}>
              <i className="fa fa-exclamation-triangle"></i>
            </div>
            <h3>{deleteModal.isAll ? '¿Borrar Todos los Pedidos?' : '¿Borrar este Pedido?'}</h3>
            <p>{deleteModal.isAll ? 'Se eliminarán absolutamente todos los registros históricos. Esta acción no se puede deshacer.' : '¿Estás seguro de que deseas eliminar este pedido permanentemente?'}</p>
            <div style={{ display: 'flex', justifyContent: 'center', gap: '15px', marginTop: '25px' }}>
              <button className="btn-secondary-light" onClick={() => setDeleteModal({ isOpen: false, id: null, isAll: false })}>Cancelar</button>
              <button className="btn-danger-light" onClick={confirmDelete}>Sí, Eliminar</button>
            </div>
          </div>
        </div>
      )}

      <Footer />
    </div>
  );
};

export default AdminDashboard;
