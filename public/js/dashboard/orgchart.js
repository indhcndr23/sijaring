document.addEventListener('DOMContentLoaded', () => {
    const wrap = document.querySelector('.org-chart-wrap');
    if (!wrap) return;

    const svg = document.getElementById('org-lines');
    const root = wrap.querySelector('.org-root-row .org-node');
    const childrenContainer = document.getElementById('org-children');
    const ns = 'http://www.w3.org/2000/svg';

    const pt = (el) => {
        const wrapRect = wrap.getBoundingClientRect();
        const r = el.getBoundingClientRect();
        return {
            top: r.top - wrapRect.top,
            bottom: r.bottom - wrapRect.top,
            left: r.left - wrapRect.left,
            right: r.right - wrapRect.left,
            centerX: (r.left - wrapRect.left) + r.width / 2
        };
    };

    const line = (x1, y1, x2, y2, dashed) => {
        const el = document.createElementNS(ns, 'line');
        el.setAttribute('x1', x1);
        el.setAttribute('y1', y1);
        el.setAttribute('x2', x2);
        el.setAttribute('y2', y2);
        el.setAttribute('stroke', '#0b0b0b');
        el.setAttribute('stroke-width', '2');
        if (dashed) el.setAttribute('stroke-dasharray', '6,5');
        svg.appendChild(el);
    };

    const drawLines = () => {
        if (!root || !childrenContainer) return;

        const children = Array.from(childrenContainer.querySelectorAll('.org-child'));
        if (!children.length) return;

        const wrapRect = wrap.getBoundingClientRect();
        svg.setAttribute('width', wrapRect.width);
        svg.setAttribute('height', wrapRect.height);
        svg.innerHTML = '';

        const rootPt = pt(root);
        const rows = [];

        children.forEach(child => {
            const node = child.querySelector('.org-node');
            if (!node) return;
            const p = pt(node);

            let row = rows.find(r => Math.abs(r.top - p.top) < 12);
            if (!row) {
                row = { top: p.top, items: [] };
                rows.push(row);
            }

            const kabelStr = (child.getAttribute('data-kabel') || '').toLowerCase();
            const hasFO = kabelStr.includes('fo');
            const hasBroadband = kabelStr.includes('broadband') || kabelStr.includes('broadband');

            row.items.push({ p, hasFO, hasBroadband });
        });

        rows.sort((a, b) => a.top - b.top);

        const trunkX = rootPt.centerX;
        let prevY = rootPt.bottom;

        rows.forEach(row => {
            const branchY = row.top - 18;
            const xs = row.items.map(it => it.p.centerX);
            const minX = Math.min(...xs);
            const maxX = Math.max(...xs);

            line(trunkX, prevY, trunkX, branchY, false);

            if (minX !== maxX) {
                line(minX, branchY, maxX, branchY, false);
            }

            row.items.forEach(it => {
                const offset = 4;
                if (it.hasFO && it.hasBroadband) {
                    line(it.p.centerX - offset, branchY, it.p.centerX - offset, it.p.top, false);
                    line(it.p.centerX + offset, branchY, it.p.centerX + offset, it.p.top, true);
                } else if (it.hasBroadband) {
                    line(it.p.centerX, branchY, it.p.centerX, it.p.top, true);
                } else {
                    line(it.p.centerX, branchY, it.p.centerX, it.p.top, false);
                }
            });

            prevY = branchY;
        });
    };

    drawLines();

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(drawLines, 100);
    });

    setTimeout(drawLines, 200);

    const tabBtn = document.getElementById('tab-grafik-btn');
    tabBtn?.addEventListener('shown.bs.tab', drawLines);
});