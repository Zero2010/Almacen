import React from 'react';

const Footer = () => {
  return (
    <footer className="footer_section">
      <div className="container">
        <div className="row">
          <div className="col-md-4 footer-col">
            <div className="footer_contact">
              <h4>Contactenos</h4>
              <div className="contact_link_box">
                <a href="" onClick={e => e.preventDefault()}>
                  <i className="fa fa-map-marker" aria-hidden="true"></i>
                  <span>Ubicación</span>
                </a>
                <a href="" onClick={e => e.preventDefault()}>
                  <i className="fa fa-phone" aria-hidden="true"></i>
                  <span>Call +01 1234567890</span>
                </a>
                <a href="" onClick={e => e.preventDefault()}>
                  <i className="fa fa-envelope" aria-hidden="true"></i>
                  <span>demo@gmail.com</span>
                </a>
              </div>
            </div>
          </div>
          <div className="col-md-4 footer-col">
            <div className="footer_detail">
              <a href="" className="footer-logo" onClick={e => e.preventDefault()}>
                Comida al paso
              </a>
              <p>
                Necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with
              </p>
              <div className="footer_social">
                <a href="" onClick={e => e.preventDefault()}>
                  <i className="fa fa-facebook" aria-hidden="true"></i>
                </a>
                <a href="" onClick={e => e.preventDefault()}>
                  <i className="fa fa-twitter" aria-hidden="true"></i>
                </a>
                <a href="" onClick={e => e.preventDefault()}>
                  <i className="fa fa-linkedin" aria-hidden="true"></i>
                </a>
                <a href="" onClick={e => e.preventDefault()}>
                  <i className="fa fa-instagram" aria-hidden="true"></i>
                </a>
                <a href="" onClick={e => e.preventDefault()}>
                  <i className="fa fa-pinterest" aria-hidden="true"></i>
                </a>
              </div>
            </div>
          </div>
          <div className="col-md-4 footer-col">
            <h4>Horario de Atención</h4>
            <p>Lunes a Viernes</p>
            <p>20.00 Pm - 02.00 Am</p>
          </div>
        </div>
        <div className="footer-info">
          <p>
            &copy; <span id="displayYear"></span> All Rights Reserved By
            <a href="https://html.design/">Free Html Templates</a><br /><br />
            &copy; <span id="displayYear"></span> Distributed By
            <a href="https://themewagon.com/" target="_blank" rel="noreferrer">ThemeWagon</a>
          </p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
