import html2canvas from 'html2canvas';

window.downloadSocialCard = async function (filename) {
    const node = document.getElementById('social-card');
    const canvas = await html2canvas(node, { scale: 2, useCORS: true });
    const link = document.createElement('a');
    link.download = filename || 'quotation-card.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
};
