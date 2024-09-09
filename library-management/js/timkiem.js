const suggestions = [
  "sách nói hay nhất",
  "sách nói",
  "sách khai huyền",
  "sách nói kinh doanh",
  "sách nói hay nhất về cuộc sống",
  "sách hay",
  "sách tóm tắt",
  "sách khôn ngoan kinh thánh",
  "sách nói muôn kiếp nhân sinh",
];

function showSuggestions() {
  const input = document.getElementById("searchInput").value.toLowerCase();
  const suggestionsBox = document.getElementById("suggestionsBox");

  // Reset lại các gợi ý
  suggestionsBox.innerHTML = "";
  suggestionsBox.classList.remove("show");

  if (input) {
    const filteredSuggestions = suggestions.filter((suggestion) =>
      suggestion.toLowerCase().includes(input)
    );

    filteredSuggestions.forEach((suggestion) => {
      const suggestionItem = document.createElement("div");
      suggestionItem.textContent = suggestion;
      suggestionItem.onclick = () => {
        document.getElementById("searchInput").value = suggestion;
        suggestionsBox.classList.remove("show"); // Ẩn gợi ý sau khi chọn
      };
      suggestionsBox.appendChild(suggestionItem);
    });

    if (filteredSuggestions.length > 0) {
      suggestionsBox.classList.add("show"); // Hiển thị khung gợi ý nếu có gợi ý
    }
  }
}

// Ẩn gợi ý khi nhấp ra ngoài
document.addEventListener("click", function (event) {
  const isClickInside =
    document.getElementById("searchInput").contains(event.target) ||
    document.getElementById("suggestionsBox").contains(event.target);

  if (!isClickInside) {
    document.getElementById("suggestionsBox").classList.remove("show");
  }
});
