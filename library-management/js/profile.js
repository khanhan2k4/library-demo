function showForm(formId) {
  // Lấy tất cả các phần tử có class 'form-section'
  const forms = document.querySelectorAll(".form-section");

  // Ẩn tất cả các form
  forms.forEach((form) => {
    form.style.display = "none";
  });

  // Hiển thị form được chọn dựa trên ID
  const selectedForm = document.getElementById(formId);
  if (selectedForm) {
    selectedForm.style.display = "block";
  }
}
