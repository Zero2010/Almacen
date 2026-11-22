import React, { createContext, useState, useEffect } from 'react';

export const OrderContext = createContext();

export const OrderProvider = ({ children }) => {
    const [orders, setOrders] = useState([]);

    // Cargar pedidos desde localStorage al iniciar
    useEffect(() => {
        const storedOrders = JSON.parse(localStorage.getItem('comida_pedidos')) || [];
        setOrders(storedOrders);
    }, []);

    // Función para agregar un nuevo pedido
    const addOrder = (newOrder) => {
        const updatedOrders = [...orders, newOrder];
        setOrders(updatedOrders);
        localStorage.setItem('comida_pedidos', JSON.stringify(updatedOrders));
    };

    // Función para eliminar un pedido (para el panel de admin)
    const removeOrder = (id) => {
        const updatedOrders = orders.filter(order => order.id !== id);
        setOrders(updatedOrders);
        localStorage.setItem('comida_pedidos', JSON.stringify(updatedOrders));
    };

    // Función para actualizar estado de un pedido
    const updateOrderStatus = (id, newStatus) => {
        const updatedOrders = orders.map(order => 
            order.id === id ? { ...order, status: newStatus } : order
        );
        setOrders(updatedOrders);
        localStorage.setItem('comida_pedidos', JSON.stringify(updatedOrders));
    };

    // Función para limpiar todos los pedidos
    const clearAllOrders = () => {
        setOrders([]);
        localStorage.removeItem('comida_pedidos');
    };

    return (
        <OrderContext.Provider value={{
            orders,
            addOrder,
            removeOrder,
            updateOrderStatus,
            clearAllOrders
        }}>
            {children}
        </OrderContext.Provider>
    );
};
