document.addEventListener("DOMContentLoaded", () => {
  const coachCards = document.querySelectorAll(".coach-card");
  const timeButtons = document.querySelectorAll(".time-slot");
  const coachIdInput = document.getElementById("coachId");
  const timeSlotInput = document.getElementById("timeSlot");
  const dateInput = document.getElementById("bookingDate");

  const summary = document.getElementById("bookingSummary");
  const summaryCoach = document.getElementById("summaryCoach");
  const summaryDate = document.getElementById("summaryDate");
  const summaryTime = document.getElementById("summaryTime");

  let selectedCoach = null;
  let selectedTime = null;

  // Coach selection
  coachCards.forEach(card => {
    card.addEventListener("click", () => {
      coachCards.forEach(c => c.classList.remove("selected"));
      card.classList.add("selected");
      selectedCoach = card.dataset.coachId;
      coachIdInput.value = selectedCoach;
      updateSummary();
    });
  });

  // Time slot selection
  timeButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      timeButtons.forEach(b => b.classList.remove("selected"));
      btn.classList.add("selected");
      selectedTime = btn.dataset.time;
      timeSlotInput.value = selectedTime;
      updateSummary();
    });
  });

  // Update summary dynamically
  dateInput.addEventListener("change", updateSummary);

  function updateSummary() {
    if (selectedCoach && dateInput.value && selectedTime) {
      summary.style.display = "block";
      const coachName = document.querySelector(
        `.coach-card[data-coach-id="${selectedCoach}"] h3`
      ).textContent;
      summaryCoach.textContent = `🏊 Coach: ${coachName}`;
      summaryDate.textContent = `📅 Date: ${dateInput.value}`;
      summaryTime.textContent = `🕐 Time: ${selectedTime}`;
    } else {
      summary.style.display = "none";
    }
  }
});
