document.querySelectorAll('.post-image').forEach((img) => {
  img.addEventListener('click', () => {
    const w = window.open('', '_blank');
    w.document.write(`<img src="${img.src}" style="max-width:100%;display:block;margin:auto">`);
  });
});
