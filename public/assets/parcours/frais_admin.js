

$(function () {


    $('.autre_frais').on('update-value', function (e, val, element) {
  
         total = 0;
       $('.autre_frais').each(function(e){
     const $this = $(this);
         //somme = somme +parseInt($this.val())
          total = total + parseInt($this.val().replaceAll(' ', ''));
       })

        $('.total').val(total)
        
    })


        
           update_totaux()

function update_totaux(){
    total = 0;
    $('.montant_echeancier').each(function(e){
  const $this = $(this);
  if ($this.val() != '') {
    total = total + parseInt($this.val().replaceAll(' ', ''));

    }
      //somme = somme +parseInt($this.val())
       
    })

     $('.col-total').text(total)
}

$('.montant_echeancier').on('update-value', function (e, val, element) {
    
    update_totaux()
   
})

        init_select2('select');
         $('.no-auto').each(function () {
            const $this = $(this);
            const $id = $('#' + $this.attr('id'));
            init_date_picker($id,  'down', (start, e) => {
                //$this.val(start.format('DD/MM/YYYY'));
            }, null, null, false);

            $id.on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY'));
            });
        });



       
        const $container = $('.row-container-frais');

        var index = $container.find('.row-colonne-frais').length;

        const $addLink = $('.add-frais');

    
        $addLink.click(function (e) {

            addLine($container);

update_totaux()
  $('.montant_echeancier').on('update-value', function (e, val, element) {
    
    update_totaux()
   
})



            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        });
    // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un (cas d'une nouvelle annonce par exemple).
   
        if (index > 0) {
            $container.children('.row-colonne-frais').each(function () {
                const $this = $(this);
                addDeleteLink($this);
                $this.find("select").each(function () {
                    const $this = $(this);
                    init_select2($this, null, '#modal-lg');
                });
            });
        }

    // La fonction qui ajoute un formulaire Categorie
        function addLine($container) {
        // Dans le contenu de l'attribut « data-prototype », on remplace :
        // - le texte "__name__label__" qu'il contient par le label du champ
        // - le texte "__name__" qu'il contient par le numéro du champ
            var $prototype = $($(".proto-container-frais").attr('data-prototype').replace(/__name__label__/g, 'Ligne ' + (
            index + 1)).replace(/__name__/g, index));
            // On ajoute au prototype un lien pour pouvoir supprimer la prestation
            addDeleteLink($prototype);
            // On ajoute le prototype modifié à la fin de la balise <div>
            $container.append($prototype);

            $prototype.find("select").each(function () {
            const $this = $(this);
                init_select2($this, null, '#modal-lg');
            });

            $prototype.find('.field-matiere').css('display', 'block');

        
            index++;

              $('.no-auto').each(function () {
       const $this = $(this);
       const $id = $('#' + $this.attr('id'));
       init_date_picker($id,  'down', (start, e) => {
           //$this.val(start.format('DD/MM/YYYY'));
       }, null, null, null);

       $id.on('apply.daterangepicker', function (ev, picker) {
           $(this).val(picker.startDate.format('DD/MM/YYYY'));
       });
   });

        }

        function addDeleteLink($prototype) {
            // Création du lien
            let $deleteLink = $('<a href="#" class="btn btn-danger btn-sm"><span class="bi bi-trash"></span></a>');
            // Ajout du lien
            if ($prototype.find('.del-col').find('.btn-danger').length == 0) {
                $prototype.find(".del-col").append($deleteLink);
            } else {
                $deleteLink = $prototype.find('.del-col').find('.btn-danger');
            }
            
            // Ajout du listener sur le clic du lien
            $deleteLink.click(function (e) {
                const $parent = $(this).closest('.row-colonne-frais');
                $parent.remove();
update_totaux()
  $('.montant_echeancier').on('update-value', function (e, val, element) {
    
    update_totaux()
   
})

                if (index > 0) {
                    index -= 1;
                }

                e.preventDefault(); // évite qu'un # apparaisse dans l'URL
            });
        }

    });