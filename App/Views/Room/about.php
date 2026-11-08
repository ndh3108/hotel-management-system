
<style>
/* HERO */
.hero {
    height: 300px;
    background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
                url('https://images.unsplash.com/photo-1566073771259-6a8506099945');
    background-size: cover;
    background-position: center;
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    text-align:center;
}

.hero h1 {
    font-size: 40px;
}

/* SECTION */
.section {
    max-width: 1100px;
    margin: 40px auto;
    padding: 0 20px;
}

/* GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px,1fr));
    gap: 20px;
}

/* CARD */
.card {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    background: white;
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

.card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.card-body {
    padding: 15px;
}

/* REVIEW */
.review {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
}

/* CTA */
.cta {
    text-align:center;
    margin:50px 0;
}

.btn {
    background:#007bff;
    color:white;
    padding:12px 25px;
    border-radius:25px;
    text-decoration:none;
}
</style>

<!-- HERO -->
<div class="hero">
    <div>
        <h1> Booking Hotel</h1>
        <p>Luxury – Comfort – Experience</p>
    </div>
</div>

<!-- GIỚI THIỆU -->
<div class="section">
    <h2>Giới thiệu khách sạn</h2>
    <p>
        Booking Hotel là khách sạn 4 sao nằm tại trung tâm thành phố,
        mang đến không gian nghỉ dưỡng sang trọng với đầy đủ tiện nghi hiện đại.
        Chúng tôi cam kết đem lại trải nghiệm tuyệt vời cho mọi khách hàng.
    </p>
</div>

<!-- DỊCH VỤ -->
<div class="section">
    <h2>Dịch vụ nổi bật</h2>

    <div class="grid">
        <div class="card">
            <img src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304">
            <div class="card-body">
                <h3>Phòng cao cấp</h3>
                <p>Thiết kế hiện đại, view đẹp</p>
            </div>
        </div>

        <div class="card">
            <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5">
            <div class="card-body">
                <h3>Nhà hàng</h3>
                <p>Ẩm thực đa dạng Á – Âu</p>
            </div>
        </div>

        <div class="card">
            <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b">
            <div class="card-body">
                <h3>Hồ bơi</h3>
                <p>Không gian thư giãn tuyệt vời</p>
            </div>
        </div>
    </div>
</div>

<!-- GALLERY -->
<div class="section">
    <h2>Hình ảnh khách sạn</h2>

    <div class="grid">
        <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2">
        <img src="https://images.unsplash.com/photo-1566665797739-1674de7a421a">
        <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa">
    </div>
</div>

<!-- REVIEW -->
<div class="section">
    <h2>Đánh giá khách hàng</h2>

    <div class="grid">
        <div class="review">
            ⭐⭐⭐⭐⭐ <br>
            "Dịch vụ tuyệt vời, phòng sạch đẹp!"
        </div>

        <div class="review">
            ⭐⭐⭐⭐⭐ <br>
            "Nhân viên thân thiện, sẽ quay lại!"
        </div>

        <div class="review">
            ⭐⭐⭐⭐⭐ <br>
            "Vị trí rất thuận tiện."
        </div>
    </div>
</div>

<!-- CTA -->
<div class="cta">
    <h2>Đặt phòng ngay hôm nay</h2>
    <a href="<?= \App\Core\url('rooms') ?>" class="btn">Xem phòng</a>
</div>  
