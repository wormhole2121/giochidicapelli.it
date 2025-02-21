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
    const alwaysSelectableDates = [
        "2024-12-23",
        "2024-12-29",
        "2024-12-30"
    ];

    const nonBookableDates = {
        7: [13, 14, 15, 16, 17], // Agosto
        8: [6, 7], // Settembre
        10: [1, 2], // Novembre
        11: [25, 26, 31], // Dicembre
        0: [1, 2, 3, 4], // Gennaio 2025
        1: [25] // Febbraio 2025

    };

    let startingDay = firstDayOfMonth.getDay();
    for (let i = 0; i < startingDay; i++) {
        const emptyDateElement = document.createElement("div");
        emptyDateElement.classList.add("date", "out-of-month");
        datesContainer.appendChild(emptyDateElement);
    }

    for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
        const date = new Date(currentYear, currentMonth, i);
        const formattedDate = `${currentYear}-${(currentMonth + 1).toString().padStart(2, '0')}-${i.toString().padStart(2, '0')}`;
        const dateElement = document.createElement("div");
        dateElement.classList.add("date");
        dateElement.setAttribute('data-date', formattedDate);

        const spanElement = document.createElement("span");
        spanElement.textContent = i;
        dateElement.appendChild(spanElement);

        const isPastDate = date < currentDate.setHours(0, 0, 0, 0);

        const isNonBookable =
            nonBookableDates[currentMonth] &&
            nonBookableDates[currentMonth].includes(i) &&
            !alwaysSelectableDates.includes(formattedDate);

        if (isPastDate || isNonBookable) {
            dateElement.classList.add("non-selectable");
        } else if (!alwaysSelectableDates.includes(formattedDate)) {
            const dayOfWeek = date.getDay();
            if (dayOfWeek === 0 || dayOfWeek === 1) {
                dateElement.classList.add("non-selectable");
            } else {
                // Selezionabili: aggiungi il listener e controlla se sono fully booked
                if (fullyBookedDates.includes(formattedDate)) {
                    dateElement.classList.add("fully-booked");
                }
                // Rendi comunque selezionabili tutte le date, incluse le fully-booked
                dateElement.addEventListener("click", () => {
                    window.location.search = `?date=${formattedDate}`;
                });
                
            }
        } else {
            // Sempre selezionabili: controlla se sono fully booked
            if (fullyBookedDates.includes(formattedDate)) {
                dateElement.classList.add("fully-booked");
            } else {
                dateElement.addEventListener("click", () => {
                    window.location.search = `?date=${formattedDate}`;
                });
            }
        }

        datesContainer.appendChild(dateElement);

        if (selectedDate && formattedDate === selectedDate.toISOString().split('T')[0]) {
            dateElement.classList.add('active');
        }
    }
}



function applyFullyBookedStyles() {
    const fullyBookedDates = [
        // Queste date devono essere passate dal backend
    ];

    const alwaysSelectableDates = [
        "2024-12-23",
        "2024-12-29",
        "2024-12-30"
    ];

    document.querySelectorAll('.date span').forEach(function (spanElement) {
        const date = spanElement.parentElement.getAttribute('data-date').trim();

        if (fullyBookedDates.includes(date)) {
            spanElement.classList.add('fully-booked');
        } else if (alwaysSelectableDates.includes(date)) {
            spanElement.classList.remove('fully-booked');
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
            selectedButton.click(); // Simula un clic sul bottone per selezionarlo
        }
    }
});



// function generateDays() {
//     const datesContainer = document.getElementById("dates");
//     datesContainer.innerHTML = '';

//     const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
//     const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0);

//     let startingDay = firstDayOfMonth.getDay();
//     for (let i = 0; i < startingDay; i++) {
//         const emptyDateElement = document.createElement("div");
//         emptyDateElement.classList.add("date", "out-of-month");
//         datesContainer.appendChild(emptyDateElement);
//     }

//     for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
//         const date = new Date(currentYear, currentMonth, i);
//         const dayOfWeek = date.getDay();
//         const dateElement = document.createElement("div");
//         dateElement.classList.add("date");
//         dateElement.setAttribute('data-date', `${currentYear}-${currentMonth + 1}-${i}`);

//         const spanElement = document.createElement("span");
//         spanElement.classList.add("day");
//         spanElement.textContent = i;

//         if (dayOfWeek !== 0 && dayOfWeek !== 1) {
//             // Solo i giorni che non sono domenica (0) o lunedì (1) sono selezionabili
//             dateElement.addEventListener("click", () => {
//                 window.location.search = `?date=${currentYear}-${(currentMonth + 1).toString().padStart(2, '0')}-${i.toString().padStart(2, '0')}`;
//             });
//         } else {
//             // Applica una classe CSS per giorni non selezionabili (ad esempio, 'non-selectable')
//             dateElement.classList.add("non-selectable");
//         }

//         dateElement.appendChild(spanElement);
//         datesContainer.appendChild(dateElement);

//         if (selectedDate && i === selectedDate.getDate() && currentMonth === selectedDate.getMonth() && currentYear === selectedDate.getFullYear()) {
//             dateElement.classList.add('active');
//         }
//     }
// }