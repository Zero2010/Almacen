import React from 'react';
import Header from '../components/Header';
import HeroSlider from '../components/HeroSlider';
import OfferSection from '../components/OfferSection';
import MenuSection from '../components/MenuSection';
import AboutSection from '../components/AboutSection';
import BookSection from '../components/BookSection';
import Footer from '../components/Footer';

const Home = () => {
  return (
    <>
      <div className="hero_area">
        <div className="bg-box">
          <img src="/images/hero-bg.jpg" alt="" />
        </div>
        <Header />
        <HeroSlider />
      </div>
      <OfferSection />
      <MenuSection />
      <AboutSection />
      <BookSection />
      <Footer />
    </>
  );
};

export default Home;
