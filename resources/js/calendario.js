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
    console.log("DOMContentLoaded event triggered"); // Debugging
    updateCurrentMonth();
    generateDays();
    applyFullyBookedStyles(); // Applica la classe fully-booked quando il DOM è pronto
});

function generateDays() {
    const datesContainer = document.getElementById("dates");
    datesContainer.innerHTML = '';

    const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
    const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0);

    const currentDate = new Date();
    const currentMonthNow = currentDate.getMonth();
    const currentYearNow = currentDate.getFullYear();

    const isPastMonth = currentYear < currentYearNow || (currentYear === currentYearNow && currentMonth < currentMonthNow);
    const isCurrentMonth = currentYear === currentYearNow && currentMonth === currentMonthNow;

    let startingDay = firstDayOfMonth.getDay();
    for (let i = 0; i < startingDay; i++) {
        const emptyDateElement = document.createElement("div");
        emptyDateElement.classList.add("date", "out-of-month");
        datesContainer.appendChild(emptyDateElement);
    }

    const nonBookableDates = {
        7: [13, 14, 15, 16, 17], // Agosto
        8: [6, 7], // Settembre
        10: [1, 2], // Novembre
        11: [25,26,31],//Dicembre
        0: [1,2]//Gennaio
    };
    

    for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
        const date = new Date(currentYear, currentMonth, i);
        const dateElement = document.createElement("div");
        dateElement.classList.add("date");
        dateElement.setAttribute('data-date', `${currentYear}-${(currentMonth + 1).toString().padStart(2, '0')}-${i.toString().padStart(2, '0')}`);

        const spanElement = document.createElement("span");
        spanElement.textContent = i;
        dateElement.appendChild(spanElement);

        const isNonBookable =
        (currentYear === 2024 && nonBookableDates[currentMonth] && nonBookableDates[currentMonth].includes(i)) ||
        (currentYear === 2025 && currentMonth === 0 && nonBookableDates[0] && nonBookableDates[0].includes(i));
        if (isPastMonth || (isCurrentMonth && i < currentDate.getDate()) || isNonBookable) {
            dateElement.classList.add("non-selectable");
        } else {
            const dayOfWeek = date.getDay();
            if (dayOfWeek !== 0 && dayOfWeek !== 1) {
                dateElement.addEventListener("click", () => {
                    window.location.search = `?date=${currentYear}-${(currentMonth + 1).toString().padStart(2, '0')}-${i.toString().padStart(2, '0')}`;
                });
            } else {
                dateElement.classList.add("non-selectable");
            }
        }

        datesContainer.appendChild(dateElement);

        if (selectedDate && i === selectedDate.getDate() && currentMonth === selectedDate.getMonth() && currentYear === selectedDate.getFullYear()) {
            dateElement.classList.add('active');
        }
    }

    // Dopo aver generato i giorni, applica la classe fully-booked
    applyFullyBookedStyles();
}

function applyFullyBookedStyles() {
    console.log("applyFullyBookedStyles function called");
    console.log("fullyBookedDates array:", fullyBookedDates); // Debugging

    const currentDate = new Date(); // Ottieni la data corrente per il confronto

    document.querySelectorAll('.date span').forEach(function(spanElement) {
        const date = spanElement.parentElement.getAttribute('data-date').trim();
        const dateToCompare = new Date(date);

        console.log(`Checking date: ${date}`); // Mostra la data formattata

        // Applica la classe fully-booked solo se la data è fully booked e non è precedente al giorno corrente
        if (fullyBookedDates.includes(date) && dateToCompare >= currentDate.setHours(0, 0, 0, 0)) {
            console.log(`Date ${date} is fully booked.`);
            spanElement.classList.add('fully-booked');
        } else {
            console.log(`Date ${date} is NOT fully booked.`);
        }
    });
}


function updateDateDropdown(selectedDate) {
    const dropdown = document.querySelector(".date-dropdown");
    let optionExists = false;

    dropdown.querySelectorAll("option").forEach(option => {
        if (option.value === selectedDate) {
            option.selected = true;
            optionExists = true;
        } else {
            option.selected = false;
        }
    });

    if (!optionExists) {
        const optionElement = document.createElement("option");
        optionElement.value = selectedDate;
        optionElement.selected = true;
        optionElement.textContent = selectedDate;

        dropdown.appendChild(optionElement);
    }
}

$(document).ready(function() {
    $("#haircut_types").select2({
        width: 'resolve'
    });
});

document.addEventListener('DOMContentLoaded', function() {
    let timeButtons = document.querySelectorAll('.time-btn');
    let selectedTimeInput = document.getElementById('selectedTime');

    timeButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            timeButtons.forEach(function(innerBtn) {
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