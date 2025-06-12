 <!-- Trang chủ -->
 <ul class="nav flex-column duongKe">
     <li class="nav-item mt-2">
         <a class="nav-link d-flex align-items-center gap-2 text-dark" href="#">
             <i class="fas fa-home"></i>
             <span>Trang chủ</span>
         </a>
     </li>
 </ul>

 <!-- Lớp học phần -->
 <div class="duongKe my-1 section-title ps-3 nav-link active d-flex align-items-center justify-content-between">
     <div class="d-flex align-items-center gap-2">
         <img src="https://placehold.co/30" width="30" height="32" alt="Class icon" style="border-radius: 50%; border: 1.5px solid #3a3a3a;" />
         <span>Tên lớp học phần</span>
     </div>
 </div>
 <div style="flex-grow: 1; overflow-y: auto;" id="scrollableClassList">
     <!-- Danh sách nội dung lớp học phần -->
     <ul class="nav flex-column mt-1" id="classList">
         <!-- Mục khác -->
         <li class="nav-item gachDuoi">
             <a class="nav-link d-flex align-items-center gap-2 text-dark" href="#">
                 <span>Bài kiểm tra trắc nghiệm</span>
             </a>
         </li>
         <li class="nav-item gachDuoi">
             <a class="nav-link d-flex align-items-center gap-2 text-dark" href="#">
                 <span>Sự kiện học trực tuyến qua Zoom</span>
             </a>
         </li>

         <ul id="menuList" class="nav flex-column">
             <!-- Danh sách bài giảng -->
             @yield('danhSachBaiGiangSidebar')

             {{-- <ul class="nav flex-column" id="menuList">
                 @foreach($chapters as $chapter)
                 <li class="nav-item gachDuoi">
                     <a href="#" class="nav-link chapter collapsed d-flex justify-content-between align-items-center text-dark">
                         <span>Chương {{ $chapter['number'] }}</span>
             <i class="fas fa-chevron-down"></i>
             </a>
             <ul class="lesson-list">
                 @foreach($chapter['lessons'] as $lesson)
                 <li class="lesson nav-item">
                     <a href="#" class="nav-link lesson-toggle collapsed d-flex justify-content-between align-items-center text-dark">
                         <span>Bài {{ $lesson['number'] }}</span>
                         <i class="fas fa-chevron-down"></i>
                     </a>
                     <ul class="subtopic-list">
                         @foreach($lesson['subtopics'] as $subtopic)
                         <li class="subtopic nav-item">
                             <a href="#" class="nav-link text-dark">Mục {{ $subtopic }}</a>
                         </li>
                         @endforeach
                     </ul>
                 </li>
                 @endforeach
             </ul>
             </li>
             @endforeach
         </ul> --}}
     </ul>
     </ul>
 </div>
