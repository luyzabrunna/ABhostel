document.addEventListener("DOMContentLoaded", ()=> {
    const favoritoSalvos = JSON.parse(localStoragegetItem("favorito")) || [];
    const cards = document.querySelectorAll('.imovel-card');

    cards.forEach((card, index) => {
        // Cria o ícone de favorito
        const favorito = document.createElement('div');
        favorito.classList.add('favorito');
        favorito.innerHTML = '<i class="fa fa-heart"></i>';
        card.prepend(favorito);
        // verifica se o imóvel já está nos favoritos
        if (favoritoSalvos.includes(index)) {
            favorito.querySelector('i').classList.add('ativo')
        }

        // Ao clicar no coração
        favorito.addEventListener('click', () => {
            const icone = favorito.querySelector('i');
            icone.classList.toggle('ativo');

            // Atualizar lista e salvar no localStorage
            if (icone.classList.contains('ativo')) {
                favoritoSalvos.push(index);
            } else {
                const pos = favoritoSalvos.index0f(index);
                if (pos !== -1) favoritoSalvos.splice(pos, 1); 
            }
            localStorage.setItem("favorito", JSON.stringify(favoritoSalvos));
        })
    })
})