 <!-- bukti transaksi -->
 <div class="modal fade" id="evidenceModal" tabindex="-1" aria-labelledby="evidenceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="evidenceModalLabel">Bukti Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img id="evidenceImage" data-image-path="{{ Storage::url($detailJournal->evidence_image) }}" alt="Evidence Image" class="img-fluid mb-3" style="max-width: 100%; height: auto;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="openImage" class="btn btn-info">Preview Bukti</button>
                </div>
            </div>
        </div>
    </div>