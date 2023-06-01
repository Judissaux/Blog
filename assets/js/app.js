import "../css/app.scss" ;

import {Dropdown} from "bootstrap";


document.addEventListener('DOMContentLoaded', () => {
    new App()
})

class App {

    constructor(){
        this.enableDropdowns();
        this.handleCommentForm();       
        
    }
    // Pour visualisation des dropdowns sur bootstrap
    enableDropdowns(){       
            const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
            dropdownElementList.map(function (dropdownToggleEl) {
                return new Dropdown(dropdownToggleEl)
            });
        }    
    
    // Pour ajout des commentaires sans rechargement de la page
    handleCommentForm() {

        const commentForm = document.querySelector('form.comment-form');
        
        if(null == commentForm) {
            return;
        }
        
        commentForm.addEventListener('submit', async(e) => { 
            // le preventDefault permet d'empécher le rechargement de la page à la soumission du formulaire
            e.preventDefault() 
            
            // Requete ( 1er parametre: Url ou l'on envoie les données ou on reçoit)
            const response = await fetch('/ajax/comments', {
                method: 'POST',
                // on crée un objet formdate et e.target permet de récupérer les données du formulaire
                body: new FormData(e.target)
                });

                // SI la réponse n'a pas reçu le code 200 on arrête.
                if(!response.ok){
                    return;
                }

                const json = await response.json();
                // si le code json est bon
                if(json.code == 'COMMENT_ADDED_SUCCESFULLY'){
                    const commentList = document.querySelector('.comment-list')
                    const commentCount = document.querySelector('.comment-count')
                    const commentContent = document.querySelector('#comment_content')
                    // Permet d'insérer le contenu json
                    commentList.insertAdjacentHTML('afterbegin', json.message);
                    commentCount.innerText = json.numberOfComments;
                    commentContent.value = '';
                }
        } )        
    }   

};