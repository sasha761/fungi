export default class FileDropZone {
  constructor({ dropArea, fileInput, previewContainer, activeClass = "is-active", maxFileSizeMB = 10, allowedFileTypes = ['image/jpeg', 'image/png', 'application/pdf'] }) {
    this.dropArea = document.querySelector(dropArea);
    this.fileInput = document.querySelector(fileInput);
    this.previewContainer = document.querySelector(previewContainer);
    this.activeClass = activeClass;
    this.files = [];
    this.maxFileSizeMB = maxFileSizeMB;  // Максимальный размер файла в MB
    this.allowedFileTypes = allowedFileTypes; 
    this.isUpdating = false;

    if (!this.dropArea || !this.fileInput) {
      console.error("Не удалось найти элементы dropArea или fileInput");
      return;
    }

    this.init();
  }

  strError(errorText) {
    const lang = document.documentElement.getAttribute('lang');
    const str = {
      'ua-UA': {
        'Invalid input format': 'Неправильний формат введення',
        'Invalid file type': 'Недопустимий тип файлу',
        'File is too large': 'Файл занадто великий (макс. 10MB)',
      },
      'ru-RU': {
        'Invalid input format': 'Неверный формат ввода',
        'Invalid file type': 'Недопустимый тип файла',
        'File is too large': 'Файл слишком большой (макс. 10MB)',
      },
      'en-GB': {
        'Invalid input format': 'Invalid input format',
        'Invalid file type': 'Invalid file type',
        'File is too large': 'File is too large (max 10MB)',
      },
      'en-US': {
        'Invalid input format': 'Invalid input format',
        'Invalid file type': 'Invalid file type',
        'File is too large': 'File is too large (max 10MB)',
      }
    }
    return str[lang][errorText];
  }

  init() {
    this.dropArea.addEventListener("click", () => this.fileInput.click());

    this.dropArea.addEventListener("dragover", (e) => {
      e.preventDefault();
      this.dropArea.classList.add(this.activeClass);
    });

    this.dropArea.addEventListener("dragleave", () => {
      this.dropArea.classList.remove(this.activeClass);
    });

    this.dropArea.addEventListener("drop", (e) => {
      e.preventDefault();
      this.dropArea.classList.remove(this.activeClass);
      this.handleFiles(e.dataTransfer.files);
    });

    this.fileInput.addEventListener("change", () => {
      if (!this.isUpdating) {
        this.handleFiles(this.fileInput.files);
      }
    });
  }

  handleFiles(fileList) {
    console.log("Файлы загружены:", fileList);
    Array.from(fileList).forEach((file) => {
      this.files.push(file);
      
      // console.log(this.files)
      this.files.find((f) => {
        console.log(f.name, file.name)
        f.name === file.name
      })

      this.displayFile(file);
    });

    this.updateFileInput();
  }

  displayFile(file) {
    const fileElement = document.createElement("div");
    fileElement.className = "c-file-preview";

    const fileReader = new FileReader();

    fileReader.onload = (e) => {
        let fileHTML; 

        if (!this.allowedFileTypes.includes(file.type)) {
          fileHTML = `<div class="c-file-preview__img is-error">
                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="128px" height="128px" viewBox="0,0,256,256"><g fill="#dc143c" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(2,2)"><path d="M64,15.21094c-4.7,0 -8.90977,2.43 -11.25977,6.5l-49.5293,85.78906c-2.35,4.07 -2.35,8.93 0,13c2.35,4.07 6.55977,6.5 11.25977,6.5h99.0586c4.7,0 8.90976,-2.43 11.25976,-6.5c2.35,-4.07 2.35,-8.93 0,-13l-49.52929,-85.78906c-2.35,-4.07 -6.55977,-6.5 -11.25977,-6.5zM64,21.21094c2.53,0 4.80055,1.31 6.06055,3.5l49.52929,85.78906c1.27,2.19 1.27,4.81 0,7c-1.27,2.19 -3.53054,3.5 -6.06054,3.5h-99.0586c-2.53,0 -4.80055,-1.31 -6.06055,-3.5c-1.27,-2.19 -1.27,-4.81 0,-7l49.5293,-85.78906c1.27,-2.19 3.53055,-3.5 6.06055,-3.5zM64,51c-1.66,0 -3,1.34 -3,3v30c0,1.66 1.34,3 3,3c1.66,0 3,-1.34 3,-3v-30c0,-1.66 -1.34,-3 -3,-3zM64,96c-1.66,0 -3,1.34 -3,3v5c0,1.66 1.34,3 3,3c1.66,0 3,-1.34 3,-3v-5c0,-1.66 -1.34,-3 -3,-3z"></path></g></g></svg>
                        <span clacc="error-msg">${this.strError('Invalid file type')}</span>
                      </div>`;
        } else if (file.size > this.maxFileSizeMB * 1024 * 1024) {
          fileHTML = `<div class="c-file-preview__img is-error">
                      <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="128px" height="128px" viewBox="0,0,256,256"><g fill="#dc143c" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(2,2)"><path d="M64,15.21094c-4.7,0 -8.90977,2.43 -11.25977,6.5l-49.5293,85.78906c-2.35,4.07 -2.35,8.93 0,13c2.35,4.07 6.55977,6.5 11.25977,6.5h99.0586c4.7,0 8.90976,-2.43 11.25976,-6.5c2.35,-4.07 2.35,-8.93 0,-13l-49.52929,-85.78906c-2.35,-4.07 -6.55977,-6.5 -11.25977,-6.5zM64,21.21094c2.53,0 4.80055,1.31 6.06055,3.5l49.52929,85.78906c1.27,2.19 1.27,4.81 0,7c-1.27,2.19 -3.53054,3.5 -6.06054,3.5h-99.0586c-2.53,0 -4.80055,-1.31 -6.06055,-3.5c-1.27,-2.19 -1.27,-4.81 0,-7l49.5293,-85.78906c1.27,-2.19 3.53055,-3.5 6.06055,-3.5zM64,51c-1.66,0 -3,1.34 -3,3v30c0,1.66 1.34,3 3,3c1.66,0 3,-1.34 3,-3v-30c0,-1.66 -1.34,-3 -3,-3zM64,96c-1.66,0 -3,1.34 -3,3v5c0,1.66 1.34,3 3,3c1.66,0 3,-1.34 3,-3v-5c0,-1.66 -1.34,-3 -3,-3z"></path></g></g></svg>
                      <span clacc="error-msg">${this.strError('File is too large')}</span>
                    </div>`;
        } else if (file.type.startsWith("image/")) {
          fileHTML = `<div class="c-file-preview__img"><img src="${e.target.result}" alt="preview"></div>`
        } else {
          fileHTML = `<div class="c-file-preview__img">${this.getFileIcon()}</div>`;
        }

        fileElement.innerHTML = `
            ${fileHTML}
            <p class="c-file-preview__title">${file.name}</p>
            <div class="c-file-preview__content">
                <p class="c-file-preview__content-date">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit', hourCycle: 'h23' })}</p>
                <p class="c-file-preview__content-size">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
            </div>
            <button class="c-file-preview__remove" data-name="${file.name}">
              <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect y="0.5" width="24" height="24" rx="12"></rect>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.02323 7.81637C7.82797 7.62111 7.51139 7.62111 7.31612 7.81637C7.12086 8.01163 7.12086 8.32821 7.31612 8.52348L11.2928 12.5001L7.31639 16.4765C7.12113 16.6718 7.12113 16.9884 7.31639 17.1836C7.51166 17.3789 7.82824 17.3789 8.0235 17.1836L11.9999 13.2072L15.9763 17.1836C16.1715 17.3789 16.4881 17.3789 16.6834 17.1836C16.8786 16.9884 16.8786 16.6718 16.6834 16.4765L12.707 12.5001L16.6836 8.52348C16.8789 8.32821 16.8789 8.01163 16.6836 7.81637C16.4884 7.62111 16.1718 7.62111 15.9765 7.81637L11.9999 11.793L8.02323 7.81637Z"></path>
              </svg>
            </button>
        `;

        this.previewContainer.appendChild(fileElement);

        fileElement.querySelector(".c-file-preview__remove").addEventListener("click", () => {
          this.removeFile(file.name, fileElement);
        });
    };

    fileReader.readAsDataURL(file);
  }

  getFileIcon() {
    const icons = `<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="48" height="48" rx="24" fill="white"/>
                    <rect x="0.5" y="0.5" width="47" height="47" rx="23.5" stroke="black" stroke-opacity="0.1"/>
                    <path d="M30 21H18C17.4477 21 17 21.4477 17 22V30C17 30.5523 17.4477 31 18 31H30C30.5523 31 31 30.5523 31 30V22C31 21.4477 30.5523 21 30 21Z" stroke="#276EF7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 26H26" stroke="#276EF7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M31.274 18H16.7259C16.4091 18 16.111 18.1502 15.9224 18.4048L15.1816 19.4048C14.6928 20.0647 15.1639 21 15.9852 21H32.0148C32.8361 21 33.3072 20.0647 32.8183 19.4048L32.0776 18.4048C31.889 18.1502 31.5909 18 31.274 18Z" stroke="#276EF7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>`
    return icons;
  }

  removeFile(fileName, fileElement) {
    this.files = this.files.filter(file => file.name !== fileName);
    fileElement.remove();
    this.updateFileInput();
    this.validateFileInput();
  }

  removeAllFiles() {
    this.files = [];
    this.fileInput.value = '';

    const filePreviewElements = document.querySelectorAll('.c-file-preview');
    filePreviewElements.forEach(item => item.remove());
    

    this.updateFileInput();
    this.validateFileInput();
  }

  validateFileInput() {
    let hasInvalidFiles = this.files.some(file => 
        !this.allowedFileTypes.includes(file.type) || file.size > this.maxFileSizeMB * 1024 * 1024
    );

    if (hasInvalidFiles) {
        this.fileInput.classList.add("invalid");
    } else {
        this.fileInput.classList.remove("invalid");
    }
  }

  updateFileInput() {
    this.isUpdating = true;

    const dataTransfer = new DataTransfer();
    this.files.forEach(file => dataTransfer.items.add(file));

    this.fileInput.files = dataTransfer.files;

    // Manually trigger 'change' event
    const event = new Event('change', { bubbles: true });
    this.fileInput.dispatchEvent(event);

    // Reset the flag after the update
    this.isUpdating = false;
  }
}
