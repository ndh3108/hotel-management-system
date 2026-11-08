<h1>Thêm dịch vụ</h1>
<form method="post" action="admin.php?page=service&action=store">
    <p><label>Tên</label> <input type="text" name="name" required></p>
    <p><label>Giá</label> <input type="number" name="price" step="0.01" min="0"></p>
    <p><label>Mô tả</label> <textarea name="description" rows="3"></textarea></p>
    <p><label>Loại</label> <select name="type">
            <option value="addon">Addon</option>
            <option value="standalone">Standalone</option>
        </select></p>
    <p><label>Kích hoạt</label> <select name="is_active">
            <option value="1">Có</option>
            <option value="0">Không</option>
        </select></p>
    <p><button type="submit">Lưu</button> <a href="admin.php?page=service" class="btn">Hủy</a></p>
</form>
