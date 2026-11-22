import React, { useState, useContext } from 'react';
import { OrderContext } from '../context/OrderContext';

const BookSection = () => {
  const { addOrder } = useContext(OrderContext);
  
  const [name, setName] = useState('');
  const [phone, setPhone] = useState('');
  const [email, setEmail] = useState('');
  const [persons, setPersons] = useState('');
  const [date, setDate] = useState('');
  const [time, setTime] = useState('');
  const [showSuccess, setShowSuccess] = useState(false);
  const [errors, setErrors] = useState({});

  const validate = () => {
    const newErrors = {};
    if (!name.trim()) newErrors.name = 'El nombre es obligatorio';
    if (!phone.trim() || phone.length < 8) newErrors.phone = 'Ingrese un teléfono válido';
    if (!persons) newErrors.persons = 'Seleccione cantidad';
    if (!date) newErrors.date = 'Seleccione una fecha';
    if (!time) newErrors.time = 'Seleccione una hora';
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if(validate()) {
      const newOrder = {
        id: Date.now().toString(),
        name,
        phone,
        email,
        persons,
        date,
        time,
        status: 'Pendiente',
        createdAt: new Date().toISOString()
      };
      
      addOrder(newOrder);
      setShowSuccess(true);
      setErrors({});
      
      // Reset
      setTimeout(() => {
        setShowSuccess(false);
        setName('');
        setPhone('');
        setEmail('');
        setPersons('');
        setDate('');
        setTime('');
      }, 3000);
    }
  };

  return (
    <section className="book_section" style={{ position: 'relative' }}>
      {/* Decorative background elements */}
      <div style={{ position: 'absolute', top: '-10%', left: '-5%', width: '300px', height: '300px', background: 'var(--primary)', filter: 'blur(150px)', opacity: '0.2', zIndex: 0 }}></div>
      <div style={{ position: 'absolute', bottom: '-10%', right: '-5%', width: '400px', height: '400px', background: 'var(--secondary)', filter: 'blur(200px)', opacity: '0.15', zIndex: 0 }}></div>

      <div className="container" style={{ position: 'relative', zIndex: 1 }}>
        <div className="heading_container mb-5 text-center animate-fade-in delay-100">
          <h2 style={{ color: '#fff', fontSize: '2.5rem', marginBottom: '10px' }}>Realiza tu <span style={{ color: 'var(--primary)' }}>Pedido</span></h2>
          <p style={{ color: 'var(--text-muted)', fontSize: '1.1rem', maxWidth: '600px', margin: '0 auto' }}>Completa el formulario para retirar tu comida favorita sin demoras.</p>
        </div>
        
        <div className="row">
          <div className="col-md-6 mb-4 mb-md-0">
            <div className="glass-panel animate-fade-in delay-200">
              <form onSubmit={handleSubmit}>
                <div className="form-group mb-4">
                  <label style={{ color: 'var(--text-muted)', fontSize: '0.9rem', marginBottom: '8px', display: 'block' }}>Nombre Completo *</label>
                  <input type="text" className={`form-control modern-input ${errors.name ? 'input-error' : ''}`} placeholder="Ej. Juan Pérez" value={name} onChange={e=>setName(e.target.value)} />
                  {errors.name && <span className="text-error">{errors.name}</span>}
                </div>
                
                <div className="form-group mb-4">
                  <label style={{ color: 'var(--text-muted)', fontSize: '0.9rem', marginBottom: '8px', display: 'block' }}>Teléfono *</label>
                  <input type="tel" className={`form-control modern-input ${errors.phone ? 'input-error' : ''}`} placeholder="+56 9 1234 5678" value={phone} onChange={e=>setPhone(e.target.value)} />
                  {errors.phone && <span className="text-error">{errors.phone}</span>}
                </div>
                
                <div className="form-group mb-4">
                  <label style={{ color: 'var(--text-muted)', fontSize: '0.9rem', marginBottom: '8px', display: 'block' }}>Correo Electrónico</label>
                  <input type="email" className="form-control modern-input" placeholder="correo@ejemplo.com" value={email} onChange={e=>setEmail(e.target.value)} />
                </div>
                
                <div className="row">
                  <div className="col-md-4 form-group mb-4">
                    <label style={{ color: 'var(--text-muted)', fontSize: '0.9rem', marginBottom: '8px', display: 'block' }}>Personas *</label>
                    <select className={`form-control modern-input ${errors.persons ? 'input-error' : ''}`} value={persons} onChange={e=>setPersons(e.target.value)} style={{ WebkitAppearance: 'none', appearance: 'none', background: 'rgba(15, 23, 42, 0.6) url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 16 16\'%3E%3Cpath fill=\'none\' stroke=\'%2394a3b8\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'m2 5 6 6 6-6\'/%3E%3C/svg%3E") no-repeat right 12px center/16px 12px' }}>
                      <option value="" disabled>Selec...</option>
                      <option value="2">2 Personas</option>
                      <option value="3">3 Personas</option>
                      <option value="4">4 Personas</option>
                      <option value="5">5 Personas</option>
                      <option value="Mas de 5">Más de 5</option>
                    </select>
                    {errors.persons && <span className="text-error">{errors.persons}</span>}
                  </div>
                  
                  <div className="col-md-4 form-group mb-4">
                    <label style={{ color: 'var(--text-muted)', fontSize: '0.9rem', marginBottom: '8px', display: 'block' }}>Fecha *</label>
                    <input type="date" className={`form-control modern-input ${errors.date ? 'input-error' : ''}`} min={new Date().toISOString().split('T')[0]} value={date} onChange={e=>setDate(e.target.value)} />
                    {errors.date && <span className="text-error">{errors.date}</span>}
                  </div>

                  <div className="col-md-4 form-group mb-4">
                    <label style={{ color: 'var(--text-muted)', fontSize: '0.9rem', marginBottom: '8px', display: 'block' }}>Hora *</label>
                    <input type="time" className={`form-control modern-input ${errors.time ? 'input-error' : ''}`} value={time} onChange={e=>setTime(e.target.value)} />
                    {errors.time && <span className="text-error">{errors.time}</span>}
                  </div>
                </div>

                <div className="mt-2">
                  <button type="submit" className="btn-primary-gradient w-100" style={{ padding: '16px', fontSize: '1.1rem' }}>
                    Confirmar Pedido
                  </button>
                </div>
              </form>

              {showSuccess && (
                <div className="mt-4" style={{ background: 'rgba(16, 185, 129, 0.1)', border: '1px solid rgba(16, 185, 129, 0.3)', color: 'var(--secondary)', padding: '16px', borderRadius: '8px', display: 'flex', alignItems: 'center', gap: '12px', animation: 'fadeIn 0.3s ease' }}>
                  <i className="fa fa-check-circle" style={{ fontSize: '24px' }}></i>
                  <div>
                    <h5 style={{ margin: 0, fontWeight: 600 }}>¡Pedido exitoso!</h5>
                    <p style={{ margin: 0, fontSize: '0.9rem' }}>Te contactaremos pronto para confirmar.</p>
                  </div>
                </div>
              )}
            </div>
          </div>
          
          <div className="col-md-6">
            <div className="glass-panel animate-fade-in delay-300" style={{ height: '100%', padding: '0', overflow: 'hidden', display: 'flex', flexDirection: 'column' }}>
              <div style={{ padding: '20px', borderBottom: '1px solid var(--glass-border)' }}>
                <h4 style={{ margin: 0, color: '#fff', fontSize: '1.2rem', display: 'flex', alignItems: 'center', gap: '10px' }}>
                  <i className="fa fa-map-marker" style={{ color: 'var(--primary)' }}></i>
                  Encuéntranos Aquí
                </h4>
              </div>
              <div style={{ flex: 1, minHeight: '350px', background: 'rgba(0,0,0,0.2)', position: 'relative' }}>
                <iframe 
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d13315.659359288863!2d-70.6558482!3d-33.4428059!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9662c5a1768bba33%3A0x7d020d2cc9504856!2sSantiago%2C%20Regi%C3%B3n%20Metropolitana!5e0!3m2!1ses-419!2scl!4v1700000000000!5m2!1ses-419!2scl" 
                  width="100%" 
                  height="100%" 
                  style={{ border: 0, filter: 'invert(90%) hue-rotate(180deg) contrast(110%) opacity(0.8)' }} 
                  allowFullScreen="" 
                  loading="lazy" 
                  referrerPolicy="no-referrer-when-downgrade"
                  title="Ubicación"
                ></iframe>
                {/* Decorative overlay to make map look more integrated with dark theme */}
                <div style={{ position: 'absolute', top: 0, left: 0, right: 0, bottom: 0, background: 'linear-gradient(rgba(15,23,42,0.1), rgba(15,23,42,0.4))', pointerEvents: 'none' }}></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default BookSection;
