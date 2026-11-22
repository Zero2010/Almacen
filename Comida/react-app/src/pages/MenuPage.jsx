import React from 'react';
import Header from '../components/Header';
import MenuSection from '../components/MenuSection';
import Footer from '../components/Footer';

const MenuPage = () => {
  return (
    <div className="sub_page">
      <div className="hero_area">
        <div className="bg-box">
          <img src="/images/hero-bg.jpg" alt="" />
        </div>
        <Header />
      </div>
      <MenuSection />
      <Footer />
    </div>
  );
};

export default MenuPage;
