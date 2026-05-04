// app.ts

document.addEventListener("DOMContentLoaded", () => {
    
    // ==========================================
    // 1. Right Panel Slide-out Logic
    // ==========================================
    const rightPanel = document.getElementById("detailPanel") as HTMLElement;
    const closeBtn = document.getElementById("closePanel") as HTMLButtonElement;
    
    const rpTitle = document.getElementById("rp-title") as HTMLHeadingElement;
    const rpStatus = document.getElementById("rp-status") as HTMLParagraphElement;
    const rpCover = document.getElementById("rp-cover") as HTMLImageElement;
    const rpDesc = document.getElementById("rp-desc") as HTMLParagraphElement; 

    const titleWrappers = document.querySelectorAll(".title-wrapper");
    
    titleWrappers.forEach(wrapper => {
        wrapper.addEventListener("click", (e) => {
            const element = e.currentTarget as HTMLElement;
            
            const title = element.getAttribute("data-title") || "Unknown Title";
            const status = element.getAttribute("data-status") || "Ongoing";
            const coverUrl = element.getAttribute("data-cover");
            const description = element.getAttribute("data-desc") || "No description available."; 

            rpTitle.textContent = title;
            rpStatus.textContent = status;
            rpDesc.textContent = description; 
            
            if (coverUrl && coverUrl !== '') {
                rpCover.src = coverUrl;
            } else {
                rpCover.src = 'https://via.placeholder.com/300x400/2a2a2a/555555?text=No+Cover';
            }

            rightPanel.classList.add("active");
        });
    });

    if (closeBtn) {
        closeBtn.addEventListener("click", () => {
            rightPanel.classList.remove("active");
        });
    }

    // ==========================================
    // 2. Import Button Loading State
    // ==========================================
    const importForm = document.getElementById("importForm") as HTMLFormElement;
    const importBtn = document.getElementById("importBtn") as HTMLButtonElement;

    if (importForm && importBtn) {
        importForm.addEventListener("submit", () => {
            setTimeout(() => {
                importBtn.textContent = "Scraping...";
                importBtn.style.backgroundColor = "var(--text-sub)";
                importBtn.disabled = true;
            }, 10);
        });
    }

    // ==========================================
    // 3. Delete Confirmation Logic
    // ==========================================
    const deleteForms = document.querySelectorAll(".delete-form");
    
    deleteForms.forEach(form => {
        form.addEventListener("submit", (e) => {
            const confirmed = confirm("Remove this from your library? Your progress and rating will be deleted.");
            if (!confirmed) {
                e.preventDefault(); 
            }
        });
    });

    // ==========================================
    // 4. Custom Sidebar Images (LocalStorage)
    // ==========================================
    const listLinks = document.querySelectorAll('.nav-link[data-list]');
    
    listLinks.forEach(link => {
        const listId = link.getAttribute('data-list');
        const img = link.querySelector('.list-cover') as HTMLImageElement;
        const editBtn = link.querySelector('.btn-edit-list') as HTMLButtonElement;

        if (listId && img) {
            // Load saved image on refresh
            const savedImage = localStorage.getItem(`sidebar_img_${listId}`);
            if (savedImage) {
                img.src = savedImage;
            }

            // Handle the edit click
            if (editBtn) {
                editBtn.addEventListener('click', (e) => {
                    e.preventDefault(); 
                    e.stopPropagation(); 
                    
                    const newUrl = prompt(`Enter a new image URL for the ${listId.replace(/_/g, ' ')} playlist:\n(Leave blank to reset to default)`);
                    
                    if (newUrl) {
                        img.src = newUrl;
                        localStorage.setItem(`sidebar_img_${listId}`, newUrl);
                    } else if (newUrl === "") {
                        // Reset to the custom gradient SVG stored in data-default-src
                        img.src = img.getAttribute('data-default-src') || "";
                        localStorage.removeItem(`sidebar_img_${listId}`);
                    }
                });
            }
        }
    });

    // ==========================================
    // 5. Real-Time Search Filtering
    // ==========================================
    const searchInput = document.getElementById("searchInput") as HTMLInputElement;
    const allRows = document.querySelectorAll(".list-container .row:not(.row-header)");

    if (searchInput) {
        searchInput.addEventListener("input", (e) => {
            const searchTerm = (e.target as HTMLInputElement).value.toLowerCase();
            
            allRows.forEach(row => {
                const titleElement = row.querySelector(".title") as HTMLParagraphElement;
                if (titleElement) {
                    const titleText = titleElement.textContent?.toLowerCase() || "";
                    if (titleText.includes(searchTerm)) {
                        (row as HTMLElement).style.display = "grid"; // Restore original grid layout
                    } else {
                        (row as HTMLElement).style.display = "none"; // Hide if it doesn't match
                    }
                }
            });
        });
    }
});