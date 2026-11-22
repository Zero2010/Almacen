import React from 'react';
import Header from '../components/Header';
import AboutSection from '../components/AboutSection';
import Footer from '../components/Footer';

const AboutPage = () => {
  return (
    <div className="sub_page">
      <div className="hero_area">
        <div className="bg-box">
          <img src="/images/hero-bg.jpg" alt="" />
        </div>
        <Header />
      </div>
      <AboutSection />
      <Footer />
    </div>
  );
};

export default AboutPage;
