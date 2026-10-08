import html2canvas from 'html2canvas';

window.downloadSocialCard = async function (filename) {
    const node = document.getElementById('social-card');
    // 540x675 preview at 2x = 1080x1350 (Instagram/Facebook portrait).
    const canvas = await html2canvas(node, { scale: 2, useCORS: true, backgroundColor: null });
    const link = document.createElement('a');
    link.download = filename || 'quotation-card.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
};
