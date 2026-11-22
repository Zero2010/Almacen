import React from 'react';
import Header from '../components/Header';
import BookSection from '../components/BookSection';
import Footer from '../components/Footer';

const BookPage = () => {
  return (
    <div className="sub_page">
      <div className="hero_area">
        <div className="bg-box">
          <img src="/images/hero-bg.jpg" alt="" />
        </div>
        <Header />
      </div>
      <BookSection />
      <Footer />
    </div>
  );
};

export default BookPage;
