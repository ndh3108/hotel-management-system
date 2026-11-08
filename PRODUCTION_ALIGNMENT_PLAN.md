# Production Alignment Plan

## Pham vi

Tai lieu nay chuyen ban tom tat yeu cau thanh ke hoach trien khai co the thuc thi tren repo hien tai.

Nguyen tac xuyen suot:

- bam MVC hien tai
- khong pha luong cu
- chi bo sung, chuan hoa, thay the dung cho
- FE + BE + Model + DB phai chay dong bo
- chi dung duy nhat `hotel.sql`
- xong luong nao moi sang luong tiep theo
- de quan ly, de mo rong, de debug, de bao tri

## 1. Muc tieu tong the

He thong can duoc dua ve mot nen tang quan ly khach san gan production voi cac dac diem:

- MVC ro controller, ro route, ro trach nhiem
- schema tap trung trong `hotel.sql`
- moi module co giao dien, nghiep vu BE, model, DB, trang thai dong bo
- booking, payment, room, service, inventory co state ro rang
- admin co the van hanh, audit, truy vet, debug
- UI/UX thong nhat xanh duong + trang, card-based, responsive

## 2. Nhung bat cap can xu ly truoc

### 2.1 Nguon su that dang bi tach manh

Hien tai repo co:

- `hotel.sql`
- nhieu `migration_*.sql`
- `db_patch.sql`
- `update_db*.sql`
- cac report danh dau "complete" khong con phu hop

Chuan moi:

- `hotel.sql` la schema duy nhat
- cac file SQL/phuc hoi cu chi de tham khao

### 2.2 Schema da nang cap nua voi

`hotel.sql` hien co 2 lop:

- lop `CREATE TABLE` legacy
- lop `ALTER TABLE` bo sung RBAC/User Profile o cuoi file

Huong xu ly:

- giu `hotel.sql`
- tiep tuc bo sung trong file nay
- khi co dieu kien, refactor lai `hotel.sql` thanh schema clean install nhung van trong chinh file nay, khong tach them file SQL moi

### 2.3 Co trung lap namespace va giao dien

Can dong bang kien truc:

- `App/Service` la noi uu tien giu lai cho domain service dang dung
- `App/Services` chi giu tam khi can tuong thich, khong mo rong them neu khong can
- `App/Views` la noi chinh
- `App/Views_Admin` danh dau legacy, khong tiep tuc nhan them man moi

## 3. Thu tu trien khai chuan

### Giai doan 1: Auth va RBAC

Muc tieu:

- dua RBAC tu ban co dinh sang dong
- giu login/register hien co
- doi middleware sang service kiem quyen trung tam

FE:

- login/register
- admin role list/create/edit
- admin permission list/create/edit
- gan role cho user
- gan permission theo role matrix
- badge role dang dung, role he thong, role het han

BE:

- auth controller
- role controller
- permission controller
- user-role controller
- permission middleware theo permission code
- access control service
- cache/version invalidate

DB:

- `roles`
- `permissions`
- `role_permissions`
- `user_roles`
- `rbac_permission_versions`

Schema muc tieu:

- `name`
- `code`
- `description`
- `is_system`
- `is_active`
- `sort_order`
- `permission_version`
- `deleted_at`

Quy tac:

- login xong load permission vao session/JWT/cache
- permission version doi thi reload
- xoa mem role/permission
- `super_admin` khong duoc xoa
- moi thay doi RBAC phai ghi audit log

Dieu kien xong:

- user dang nhap xong mang dung permission
- admin CRUD role/permission on dinh
- route guard dung
- role he thong an toan

### Giai doan 1B: User/Profile

Muc tieu:

- tach account va thong tin ca nhan

FE:

- user list
- edit user
- profile tab
- roles tab
- booking history tab
- activity history tab

BE:

- user controller
- profile update service
- change password
- forgot/reset password
- verify email/phone
- log thay doi nhay cam

DB:

- `users`
- `user_profiles`
- `user_session_logs`
- `account_activity_logs`
- `password_reset_tokens`

Quy tac:

- `users` = dang nhap
- `user_profiles` = thong tin mo rong
- unique email/phone
- password hash
- gioi han doi mat khau lien tiep
- log thay doi email/phone/password/status

Dieu kien xong:

- tao/sua user on
- profile tach ro voi account
- log du thay doi quan trong

### Giai doan 2: Room va Room Type

Muc tieu:

- bien room type thanh policy object
- room co lifecycle ro

FE:

- room type list/form
- room list/form
- bo loc theo loai phong, gia, trang thai
- anh chinh, anh phu
- badge trang thai ro

BE:

- room controller
- room type controller
- room search/filter service
- room status change log

DB muc tieu:

`room_types` them:

- `base_price`
- `max_occupancy`
- `bed_type`
- `room_size`
- `amenities_default`
- `refund_policy`
- `min_stay`
- `max_stay`
- `seasonal_pricing_rule`

`rooms.status` chuan:

- `available`
- `reserved`
- `occupied`
- `cleaning`
- `maintenance`
- `blocked`
- `out_of_service`
- `damaged`

Dieu kien xong:

- room type dung duoc nhu policy object
- room status co lifecycle ro
- co log thay doi phong

### Giai doan 2B: Booking

Muc tieu:

- chot booking thanh state machine rieng
- khong tron voi payment

FE:

- form dat phong
- booking detail
- timeline trang thai
- check-in/check-out
- phi phat sinh
- tong tien
- action theo state

BE:

- booking controller/service
- availability check
- overlap check
- transaction + lock
- gia tien
- invoice generation hook

DB muc tieu:

`booking_status`:

- `draft`
- `pending_payment`
- `confirmed`
- `checked_in`
- `checked_out`
- `completed`
- `cancelled`
- `expired`
- `no_show`

`payment_status`:

- `unpaid`
- `deposit_pending`
- `partial_paid`
- `paid`
- `overdue`
- `refunding`
- `refunded`
- `failed`

Thuat toan bat buoc:

- overlap: `existing.check_in < new.check_out` va `existing.check_out > new.check_in`
- transaction
- `FOR UPDATE`
- lock theo thu tu `room_id` tang dan
- retry deadlock

Dieu kien xong:

- booking state machine ro
- check-in/check-out ro
- tong tien va phi phat sinh ro

### Giai doan 3: Inventory va Availability

Muc tieu:

- quan ly phong theo thoi gian thuc va lich su

FE:

- tong quan phong trong/giu/dang o/dang don
- bang snapshot theo ngay
- canh bao hu hong/that thoat

BE:

- availability service
- inventory ledger service
- room status updater
- event/notification publisher

DB muc tieu:

- availability projection theo `room_id`, `date`, `status`
- inventory ledger theo event
- room status log gan voi actor, reason, booking/issue

Dieu kien xong:

- xac dinh phong trong/bat chinh xac
- co snapshot va lich su
- truy vet duoc ton that/hu hong/bao tri

### Giai doan 3B: Service va Add-on

Muc tieu:

- tach dich vu khoi booking phong
- checkout thi gom chung invoice

FE:

- danh sach service
- form tao/sua service
- dat service kem booking
- status service booking

BE:

- service catalog controller
- service booking controller
- capacity checker
- pricing by package/time
- invoice integration

DB muc tieu:

`services`:

- ten
- gia
- suc chua
- loai
- trang thai
- mo ta
- hinh anh

`service_bookings.status`:

- `requested`
- `reserved`
- `active`
- `completed`
- `cancelled`
- `expired`

Dieu kien xong:

- dat rieng hoac kem booking deu duoc
- gia chot tai luc dat
- checkout gom duoc service

### Giai doan 4: Payment va Invoice

Muc tieu:

- tach ro invoice va payment transaction

FE:

- man thanh toan
- man hoa don
- lich su thanh toan
- hien coc, da tra, con lai, phat sinh

BE:

- payment controller
- payment service
- callback handler
- invoice calculator
- payment ledger writer

DB muc tieu:

`invoices`:

- `booking_id`
- `room_amount`
- `service_amount`
- `addon_amount`
- `late_fee`
- `damage_fee`
- `discount_amount`
- `prepaid_amount`
- `final_total`
- `balance_due`
- `status`

`payment_transactions` hoac nang cap bang `payments` theo vai tro transaction:

- `invoice_id`
- `gateway`
- `external_txn_id`
- `amount`
- `status`
- `paid_at`
- `raw_payload`

Quy tac:

- khong cap nhat tien truc tiep o nhieu noi
- moi phep tinh di qua invoice calculator
- callback chong lap
- verify signature
- log request/response day du

Dieu kien xong:

- dat coc truoc duoc
- phat sinh them duoc
- checkout chot tien con lai duoc
- hoa don ro va dung

### Giai doan 5: Review va Loyalty

Review:

- chi sau khi booking `completed`
- moi booking 1 review
- co cua so thoi gian review
- co anti-spam va moderation neu can

Loyalty:

- diem, hang thanh vien, lich su diem
- ap dung vao booking/invoice theo rule ro

### Giai doan 6: Admin Dashboard va System Support

Muc tieu:

- admin la trung tam dieu hanh
- debug production duoc

FE:

- dashboard tong quan
- tab/filter theo module
- man log timeline
- chi tiet before/after

BE:

- dashboard statistics service
- logger service
- audit service
- event log service
- payment log service
- security log service

Log schema muc tieu:

- `trace_id`
- `actor_id`
- `role`
- `action`
- `resource_type`
- `resource_id`
- `before_json`
- `after_json`
- `status`
- `severity`
- `ip`
- `user_agent`
- `created_at`

### Giai doan 7: UI/UX chuan hoa toan he thong

Nguyen tac:

- xanh duong + trang
- card-based
- responsive
- badge trang thai ro
- form tinh gon

Layout:

- sidebar trai
- topbar tren
- content phai
- breadcrumb
- page title
- filter row
- content card
- pagination/action bar

## 4. Quy tac khi sua code tu nay

1. Khong nhay giai doan.
2. Module truoc chua xong khong nhay sang module sau.
3. Moi thay doi phai co route + controller + service/model + query DB + UI.
4. Moi thay doi schema phai cap nhat thang vao `hotel.sql`.
5. Khong them them file SQL roi rac neu khong bat buoc.
6. Neu can tuong thich luong cu, uu tien wrapper/fallback thay vi dap bo code cu.

## 5. Danh sach viec can lam ngay

1. Chot tai lieu nay lam roadmap chinh.
2. Don dep tai lieu/report cu de tranh doi bam nham.
3. Chot schema RBAC va User/Profile trong `hotel.sql` theo muc tieu Giai doan 1.
4. Sau do moi bat tay implement code theo module, tung luong mot.
