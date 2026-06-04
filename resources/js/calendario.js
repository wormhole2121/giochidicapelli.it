let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let selectedDate = null;

function getSelectedDateFromUrl() {
    const currentParams = new URLSearchParams(window.location.search);
    return currentParams.get("date");
}

const params = new URLSearchParams(window.location.search);

if (params.has("date")) {
    selectedDate = new Date(params.get("date"));
    currentMonth = selectedDate.getMonth();
    currentYear = selectedDate.getFullYear();
}

function updateCurrentMonth() {
    const monthNames = [
        "Gennaio",
        "Febbraio",
        "Marzo",
        "Aprile",
        "Maggio",
        "Giugno",
        "Luglio",
        "Agosto",
        "Settembre",
        "Ottobre",
        "Novembre",
        "Dicembre"
    ];

    document.getElementById("currentMonth").textContent = monthNames[currentMonth] + " " + currentYear;
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

function getDefaultScheduleLabel(dayOfWeek) {
    if (dayOfWeek === 0 || dayOfWeek === 1) {
        return "Chiuso";
    }

    if (dayOfWeek === 4) {
        return "Orario giovedì 14:00-20:30";
    }

    return "Orario normale del giorno";
}

function getInputOptions(dayOfWeek) {
    const inputOptions = {
        closed: "Chiuso",
        normal: "Orario esteso 08:30-11:30 / 14:00-20:30"
    };

    if (dayOfWeek === 0 || dayOfWeek === 1 || dayOfWeek === 4) {
        inputOptions.thursday = "Orario giovedì 14:00-20:30";
    }

    return inputOptions;
}

function getCurrentInputValue(isUnavailable, scheduleOverride, dayOfWeek) {
    if (isUnavailable) {
        return "closed";
    }

    if (scheduleOverride === "normal") {
        return "normal";
    }

    if (scheduleOverride === "thursday") {
        return "thursday";
    }

    if (dayOfWeek === 4) {
        return "thursday";
    }

    if (dayOfWeek === 0 || dayOfWeek === 1) {
        return "closed";
    }

    return "normal";
}

function getActionToSend(selectedAction, dayOfWeek) {
    const isThursday = dayOfWeek === 4;

    if (isThursday && selectedAction === "thursday") {
        return "default";
    }

    return selectedAction;
}

function reloadPageOnDate(formattedDate) {
    window.location.href = `${window.location.pathname}?date=${formattedDate}`;
}

function generateDays() {
    const datesContainer = document.getElementById("dates");
    datesContainer.innerHTML = "";

    const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
    const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0);

    const unavailableDates = window.unavailableDates || [];
    const scheduleOverrides = window.scheduleOverrides || {};
    const fullyBookedDates = window.fullyBookedDates || [];
    const isAdmin = !!window.isAdmin;

    const startingDay = firstDayOfMonth.getDay();

    for (let i = 0; i < startingDay; i++) {
        const emptyDateElement = document.createElement("div");
        emptyDateElement.classList.add("date", "out-of-month");
        datesContainer.appendChild(emptyDateElement);
    }

    for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
        const date = new Date(currentYear, currentMonth, i);
        const formattedDate = `${currentYear}-${(currentMonth + 1).toString().padStart(2, "0")}-${i.toString().padStart(2, "0")}`;

        const wrapper = document.createElement("div");
        wrapper.classList.add("day-wrapper");
        wrapper.style.position = "relative";
        wrapper.dataset.date = formattedDate;

        const dayOfWeek = date.getDay();
        const isSundayOrMonday = dayOfWeek === 0 || dayOfWeek === 1;
        const isUnavailable = unavailableDates.includes(formattedDate);
        const hasScheduleOverride = Object.prototype.hasOwnProperty.call(scheduleOverrides, formattedDate);
        const scheduleOverride = hasScheduleOverride ? scheduleOverrides[formattedDate] : null;
        const isPastDate = date < new Date().setHours(0, 0, 0, 0);

        const dateElement = document.createElement("div");
        dateElement.classList.add("date");
        dateElement.setAttribute("data-date", formattedDate);

        const spanElement = document.createElement("span");
        spanElement.textContent = i;
        dateElement.appendChild(spanElement);

        const isBlockedForUsers = isPastDate || isUnavailable || (isSundayOrMonday && !hasScheduleOverride);

        if (isBlockedForUsers) {
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

        if (hasScheduleOverride) {
            dateElement.classList.add("special-schedule");
        }

        if (isAdmin && !isPastDate) {
            const icon = document.createElement("span");
            icon.className = "lock-icon";

            if (isUnavailable || (isSundayOrMonday && !hasScheduleOverride)) {
                icon.innerText = "🔒";
            } else if (scheduleOverride === "normal") {
                icon.innerText = "🕑";
            } else if (scheduleOverride === "thursday" || dayOfWeek === 4) {
                icon.innerText = "🕔";
            } else {
                icon.innerText = "🔓";
            }

            icon.style.position = "absolute";
            icon.style.top = "-30px";
            icon.style.left = "50%";
            icon.style.transform = "translateX(-50%)";
            icon.style.cursor = "pointer";
            icon.style.fontSize = "1.2em";
            icon.style.zIndex = "10";

            icon.addEventListener("click", async e => {
                e.stopPropagation();

                const defaultLabel = getDefaultScheduleLabel(dayOfWeek);
                const inputOptions = getInputOptions(dayOfWeek);
                const inputValue = getCurrentInputValue(isUnavailable, scheduleOverride, dayOfWeek);

                const result = await Swal.fire({
                    title: `Gestisci ${formattedDate}`,
                    text: `Orario predefinito: ${defaultLabel}`,
                    input: "select",
                    inputOptions: inputOptions,
                    inputValue: inputValue,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#E74C3C",
                    cancelButtonColor: "#aaa",
                    confirmButtonText: "Salva",
                    cancelButtonText: "Annulla"
                });

                if (!result.isConfirmed) return;

                const selectedAction = result.value;
                const actionToSend = getActionToSend(selectedAction, dayOfWeek);

                try {
                    const response = await fetch("/admin/toggle-date", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            date: formattedDate,
                            action: actionToSend
                        })
                    });

                    const json = await response.json();

                    if (!response.ok) {
                        Swal.fire({
                            icon: "error",
                            title: "Errore",
                            text: json.error || "Operazione non riuscita."
                        });

                        return;
                    }

                    const unavailableIndex = unavailableDates.indexOf(formattedDate);

                    if (json.status === "closed") {
                        if (unavailableIndex === -1) {
                            unavailableDates.push(formattedDate);
                        }

                        delete scheduleOverrides[formattedDate];
                    }

                    if (json.status === "special") {
                        if (unavailableIndex > -1) {
                            unavailableDates.splice(unavailableIndex, 1);
                        }

                        scheduleOverrides[formattedDate] = json.schedule_type;
                    }

                    if (json.status === "default") {
                        if (unavailableIndex > -1) {
                            unavailableDates.splice(unavailableIndex, 1);
                        }

                        delete scheduleOverrides[formattedDate];
                    }

                    const currentSelectedDate = getSelectedDateFromUrl();

                    if (currentSelectedDate === formattedDate) {
                        await Swal.fire({
                            icon: "success",
                            title: "Orari aggiornati!",
                            timer: 600,
                            showConfirmButton: false
                        });

                        reloadPageOnDate(formattedDate);
                        return;
                    }

                    await Swal.fire({
                        icon: "success",
                        title: "Salvato!",
                        timer: 600,
                        showConfirmButton: false
                    });

                    generateDays();

                } catch (error) {
                    Swal.fire({
                        icon: "error",
                        title: "Errore di rete",
                        text: "Impossibile comunicare col server."
                    });
                }
            });

            wrapper.appendChild(icon);
        }

        if (getSelectedDateFromUrl() && formattedDate === getSelectedDateFromUrl()) {
            dateElement.classList.add("active");
        }

        wrapper.appendChild(dateElement);
        datesContainer.appendChild(wrapper);
    }
}

$(document).ready(function () {
    $("#haircut_types").select2({
        width: "resolve"
    });
});