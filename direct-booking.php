<p>
    Friends or family coming to stay at your HostKeep managed property? Please let us know below. Requests take up to 24hrs to be processed and calendar blocked out.
</p>

<p>
    <strong>Please note, the calendar does not display existing guest booking. If there is an conflict with dates, we will be in touch.</strong>
</p>

<div id='directBookingAddNewBooking'>
    <table>
        <tr>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <tr>
            <td colspan="2">
                <p>
                    <strong>Property</strong>
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <select id='directBookingAddProperty'></select>
            </td>
        </tr>

        <tr class='bookingSeparator'>
            <td colspan="3">
                <div></div>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <p>
                    <strong>Guest Details</strong>
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                Guest Name
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input id='directBookingAddGuestName' type='text' />
            </td>
        </tr>

        <tr>
            <td>
                Guest Mobile
            </td>
            <td>
                Guest Email
            </td>
        </tr>
        <tr>
            <td>
                <input id='directBookingAddGuestMobile' type='text' />
            </td>
            <td>
                <input id='directBookingAddGuestEmail' type='text' />
            </td>
        </tr>

        <tr class='bookingSeparator'>
            <td colspan="3">
                <div></div>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <p>
                    <strong>Dates</strong>
                </p>
            </td>
        </tr>
        <tr>
            <td>
                Check-in Date
            </td>
            <td>
                Check-out Date
            </td>
        </tr>
        <tr>
            <td>
                <input id='directBookingAddCheckIn' type='date' />
            </td>
            <td>
                <input id='directBookingAddCheckOut' type='date' />
            </td>
        </tr>

        <tr class='bookingSeparator'>
            <td colspan="3">
                <div></div>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <p>
                    <strong>Invoice & nightly rate</strong>
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <p>Guest to be invoiced for booking:</p>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <form>
                    <input type='radio' name='invoice' id='directBookingAddInvoiceYes' checked /> Yes<br />
                    <input type='radio' name='invoice' id='directBookingAddInvoiceNo' /> No
                </form>
            </td>
        </tr>
        <tr id='nightlyRateRow'>
            <td colspan="2">
                <p>Nightly Rate: <input id='directBookingNightlyRate' type='number' /></p>
            </td>
        </tr>

        <tr class='bookingSeparator'>
            <td colspan="3">
                <div></div>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <p>Turnover (cleaning and laundry) to be completed by:</p>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <form>
                    <input type='radio' name='cleaning' id='directBookingAddInvoiceHostkeep' checked /> HostKeep (invoiced to guest)<br />
                    <input type='radio' name='cleaning' id='directBookingAddInvoiceHostkeepBilled'  /> HostKeep (owner billed)<br />
                    <input type='radio' name='cleaning' id='directBookingAddInvoiceGuest' /> Guest
                </form>
            </td>
        </tr>

        <tr class='bookingSeparator'>
            <td colspan="3">
                <div></div>
            </td>
        </tr>

        <tr>
            <td colspan="2" style='padding-top: 10px'>
                Notes
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <textarea id='directBookingAddNotes' rows='4'></textarea>
            </td>
        </tr>
    </table>

    <div>
        <button id='directBookingAddButton'>Add New Booking</button>
    </div>
</div>

<div id='directBookingDivider'>

</div>

<table id='bookingTable'>
    <thead>
        <tr>
            <th>
                Property
            </th>
            <th>
                Guest
            </th>
            <th>
                Check-In
            </th>
            <th>
                Check-Out
            </th>
            <th>
                Invoice
            </th>
            <th>
                Turnover
            </th>
            <th>
                Nightly Rate
            </th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>
