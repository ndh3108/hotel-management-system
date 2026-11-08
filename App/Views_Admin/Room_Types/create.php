<h1>Thêm loại phòng</h1>
<form method="post" action="admin.php?page=room-type&action=store">
    <p><label>Tên</label> <input type="text" name="name" required></p>
    <p><label>Sức chứa</label> <input type="number" name="capacity" min="1" value="1"></p>
    <p><label>Giá gốc</label> <input type="number" name="base_price" step="0.01" min="0"></p>
    <p><button type="submit">Lưu</button> <a href="admin.php?page=room-type" class="btn">Hủy</a></p>
</form>
