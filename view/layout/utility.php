<div class="accordion" id="utility">
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingOne">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
        WhatsApp
      </button>
    </h2>
    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
      <div class="accordion-body">
  <div class="form-floating mb-3">
    <input type="tel" class="form-control waInputTel" name="waTel" id="waTel_<?=$idx;?>" placeholder="<?=L('card.name');?>" value="<?=$cardItem['cardName'];?>" required>
    <label for="waTel_<?=$idx;?>" class="form-label"><?=L('card.name');?></label>
  </div>

      </div>
    </div>
  </div>
</div>