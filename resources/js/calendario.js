let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let selectedDate = null;

const params = new URLSearchParams(window.location.search);
if (params.has('date')) {
    selectedDate = new Date(params.get('date'));
    currentMonth = selectedDate.getMonth();
    currentYear = selectedDate.getFullYear();
}

function updateCurrentMonth() {
    const monthNames = ["Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"];
    document.getElementById("currentMonth").textContent = monthNames[currentMonth] + ' ' + currentYear;
}

function changeMonth(delta) {
    currentMonth += delta;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    } else if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    updateCurrentMonth();
    generateDays();
}

document.getElementById("prevMonth").addEventListener("click", () => {
    changeMonth(-1);
});

document.getElementById("nextMonth").addEventListener("click", () => {
    changeMonth(1);
});

window.addEventListener("DOMContentLoaded", () => {
    updateCurrentMonth();
    generateDays();
});

function generateDays() {
    const datesContainer = document.getElementById("dates");
    datesContainer.innerHTML = '';

    const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
    const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0);

    const currentDate = new Date();
    const unavailableDates = window.unavailableDates || [];
    const fullyBookedDates = window.fullyBookedDates || [];
    const isAdmin = !!window.isAdmin;

    let startingDay = firstDayOfMonth.getDay();
    for (let i = 0; i < startingDay; i++) {
        const emptyDateElement = document.createElement("div");
        emptyDateElement.classList.add("date", "out-of-month");
        datesContainer.appendChild(emptyDateElement);
    }

    for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
        const date = new Date(currentYear, currentMonth, i);
        const formattedDate = `${currentYear}-${(currentMonth + 1).toString().padStart(2, '0')}-${i.toString().padStart(2, '0')}`;
        const wrapper = document.createElement("div");
        wrapper.classList.add("day-wrapper");
        wrapper.style.position = "relative";
        wrapper.dataset.date = formattedDate;

        const dayOfWeek = date.getDay();
        const isSundayOrMonday = (dayOfWeek === 0 || dayOfWeek === 1);
        const isPastDate = date < new Date().setHours(0, 0, 0, 0);

        const dateElement = document.createElement("div");
        dateElement.classList.add("date");
        dateElement.setAttribute('data-date', formattedDate);

        const spanElement = document.createElement("span");
        spanElement.textContent = i;
        dateElement.appendChild(spanElement);

        const isUnavailable = unavailableDates.includes(formattedDate);

        if (isPastDate || isUnavailable || isSundayOrMonday) {
            dateElement.classList.add("non-selectable");
            dateElement.style.pointerEvents = "none";
            dateElement.style.cursor = "default";
        } else {
            if (fullyBookedDates.includes(formattedDate)) {
                dateElement.classList.add("fully-booked");
            }
            dateElement.addEventListener("click", () => {
                window.location.search = `?date=${formattedDate}`;
            });
        }

        if (isAdmin && !isSundayOrMonday && !isPastDate) {
            const icon = document.createElement("span");
            icon.className = "lock-icon";
            icon.innerText = unavailableDates.includes(formattedDate) ? "🔒" : "🔓";
            icon.style.position = "absolute";
            icon.style.top = "-30px";
            icon.style.left = "50%";
            icon.style.transform = "translateX(-50%)";
            icon.style.cursor = "pointer";
            icon.style.fontSize = "1.2em";
            icon.style.zIndex = "10";

            icon.addEventListener("click", async e => {
                e.stopPropagation();

                const isCurrentlyBlocked = unavailableDates.includes(formattedDate);

                if (!isCurrentlyBlocked) {
                    const result = await Swal.fire({
                        title: 'Bloccare il giorno?',
                        text: `Sei sicuro di voler bloccare il giorno ${formattedDate}? Gli utenti non potranno più prenotare in questa data.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#E74C3C',
                        cancelButtonColor: '#aaa',
                        confirmButtonText: 'Sì, blocca',
                        cancelButtonText: 'Annulla'
                    });

                    if (!result.isConfirmed) return;
                }

                try {
                    const response = await fetch("/admin/toggle-date", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ date: formattedDate })
                    });

                    const json = await response.json();

                    if (json.status === "blocked") {
                        if (!unavailableDates.includes(formattedDate)) unavailableDates.push(formattedDate);
                        Swal.fire({
                            icon: 'success',
                            title: 'Bloccato',
                            text: `Il giorno ${formattedDate} è stato bloccato con successo.`,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else if (json.status === "unblocked") {
                        const idx = unavailableDates.indexOf(formattedDate);
                        if (idx > -1) unavailableDates.splice(idx, 1);
                        Swal.fire({
                            icon: 'success',
                            title: 'Sbloccato',
                            text: `Il giorno ${formattedDate} è stato sbloccato con successo.`,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }

                    generateDays();

                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Errore di rete',
                        text: 'Impossibile comunicare con il server.',
                        confirmButtonColor: '#E74C3C'
                    });
                }
            });

            wrapper.appendChild(icon);
        }

        if (selectedDate && formattedDate === selectedDate.toISOString().split('T')[0]) {
            dateElement.classList.add('active');
        }

        wrapper.appendChild(dateElement);
        datesContainer.appendChild(wrapper);
    }
}

function applyFullyBookedStyles() {
    const fullyBookedDates = [];
    document.querySelectorAll('.date span').forEach(function (spanElement) {
        const date = spanElement.parentElement.getAttribute('data-date').trim();
        if (fullyBookedDates.includes(date)) {
            spanElement.classList.add('fully-booked');
        }
    });
}

$(document).ready(function () {
    $("#haircut_types").select2({
        width: 'resolve'
    });
});

document.addEventListener('DOMContentLoaded', function () {
    let timeButtons = document.querySelectorAll('.time-btn');
    let selectedTimeInput = document.getElementById('selectedTime');

    timeButtons.forEach((btn) => {
        btn.addEventListener('click', function () {
            timeButtons.forEach((innerBtn) => {
                innerBtn.classList.remove('btn-primary');
                innerBtn.classList.add('btn-outline-primary');
            });
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-primary');
            selectedTimeInput.value = btn.getAttribute('data-time');
        });
    });

    if (selectedTimeInput.value) {
        let selectedButton = document.querySelector(`.time-btn[data-time="${selectedTimeInput.value}"]`);
        if (selectedButton) {
            selectedButton.click();
        }
    }
});
