import React from 'react';

const HeroSlider = () => {
  return (
    <section className="slider_section ">
      <div id="customCarousel1" className="carousel slide" data-ride="carousel">
        <div className="carousel-inner">
          <div className="carousel-item active">
            <div className="container ">
              <div className="row">
                <div className="col-md-8 col-lg-5">
                  <div className="detail-box">
                    <h1>Nuestras Promociones 2 X 1 en Sandwich</h1>
                    <p>
                      Los sándwiches chilenos son una parte importante de la comida chilena y destacan por su variedad de sabores. 
                      Algunos de los más conocidos son el Barros Luco, el Chacarero, el Ave Palta y el Mechada.
                    </p>
                    <div className="btn-box">
                      <a href="/book" className="btn1">Ordene ahora</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div className="carousel-item ">
            <div className="container ">
              <div className="row">
                <div className="col-md-8 col-lg-5">
                  <div className="detail-box">
                    <h1>Nuestras Promociones 2 X 1 en Vianesas</h1>
                    <p>
                      Los completos chilenos son una versión única de los hot dogs en Chile. Están hechos con vienesas de alta calidad, 
                      pan suave y se acompañan con ingredientes como salsa de tomate, mayonesa, palta, chucrut, cebolla y ají. 
                      Son muy populares y forman parte importante de la gastronomía chilena.
                    </p>
                    <div className="btn-box">
                      <a href="/book" className="btn1">Ordene Ahora</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div className="carousel-item">
            <div className="container ">
              <div className="row">
                <div className="col-md-8 col-lg-5">
                  <div className="detail-box">
                    <h1>Promociones en pizzas familiares</h1>
                    <p>
                      La pizza es un plato icónico de la cocina italiana que ha ganado popularidad a nivel mundial. 
                      Consiste en una masa plana horneada, cubierta con salsa de tomate, queso y diversos ingredientes como carnes, vegetales y especias. Es versátil y se puede encontrar en una variedad de estilos y sabores. 
                      La pizza se disfruta ampliamente en restaurantes, pizzerías y es una opción común para llevar o hacer en casa.
                    </p>
                    <div className="btn-box">
                      <a href="/book" className="btn1">Ordene Ahora</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="container">
          <ol className="carousel-indicators">
            <li data-target="#customCarousel1" data-slide-to="0" className="active"></li>
            <li data-target="#customCarousel1" data-slide-to="1"></li>
            <li data-target="#customCarousel1" data-slide-to="2"></li>
          </ol>
        </div>
      </div>
    </section>
  );
};

export default HeroSlider;
