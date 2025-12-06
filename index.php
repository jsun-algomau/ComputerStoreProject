<?php
$title = "Home";
require_once "includes/header.php";
?>

<div class="home-page-wrapper py-5">

  <div class="container">

    <div id="homeCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">

      <div class="carousel-inner rounded overflow-hidden">

        <div class="carousel-item active">
          <img src="images/home/home_1.jpg" class="d-block w-100 home-carousel-img" alt="Slide 1">
        </div>

        <div class="carousel-item">
          <img src="images/home/home_2.jpg" class="d-block w-100 home-carousel-img" alt="Slide 2">
        </div>

        <div class="carousel-item">
          <img src="images/home/home_3.jpg" class="d-block w-100 home-carousel-img" alt="Slide 3">
        </div>

      </div>

      <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>

      <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>

    </div>

  </div>

</div>


<?php
require_once "includes/footer.php";
?>
