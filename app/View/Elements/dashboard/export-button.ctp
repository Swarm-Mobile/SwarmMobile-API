<div class="btn-group pull-right hidden-print" style="margin-left: 8px;">
    <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
        Export <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        <li role="presentation" class="dropdown-header">Export:</li>
        <li><a href="#" id="csvButton">As .CSV</a></li>
        <li><a href="#" id="pdfButton">As .PDF</a></li>
        <li><a href="#" class="emailPdfButton">To Email</a></li>
    </ul>
</div>
<div class="modal hidden-print" id="emailModal">
    <div class="modal-dialog">
        <div class="alert alert-neutral primary" style="margin:0 auto; padding: 20px 50px;">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="primaryBold">Export to Email</h4>
            <form id="exportEmailForm" role="form">
                <div class="form-group">
                    <label for="toEmail">To Email *:</label>
                    <input type="email" class="form-control" id="toEmail" placeholder="Enter email" required>
                </div>
                <div class="form-group">
                    <label for="emailMessage">Message</label>
                    <textarea id="emailMessage" class="form-control">Please see the attached PDF export from Swarm.</textarea>
                </div>
                <div class="clear spacing"></div>
                <button id="cancelEmailPdf" class="btn btn-danger pull-left black" data-dismiss="modal">Cancel</button>
                <button id="sendEmailPdf" type="submit" class="btn btn-primary pull-right">Send</button>
            </form>
            <div class="clearfix"></div>
        </div>
    </div>
</div>