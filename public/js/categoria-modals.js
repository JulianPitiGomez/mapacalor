// ===== FUNCIÓN AUXILIAR PARA ANCLAS =====
let currentSection = '';

function saveCurrentSection(section) {
    currentSection = section;
}

// ===== SUBCATEGORÍAS =====
function openSubcategoriaModal() {
    saveCurrentSection('subcategorias');
    document.getElementById('subcategoriaModalTitle').textContent = 'Agregar Subcategoría';
    document.getElementById('subcategoria_id').value = '';
    document.getElementById('subcategoria_nombre').value = '';
    document.getElementById('subcategoria_descripcion').value = '';
    document.getElementById('subcategoria_activo').checked = true;
    document.getElementById('subcategoriaModal').classList.remove('hidden');
}

function closeSubcategoriaModal() {
    document.getElementById('subcategoriaModal').classList.add('hidden');
}

function editSubcategoria(id, nombre, descripcion, activo) {
    saveCurrentSection('subcategorias');
    document.getElementById('subcategoriaModalTitle').textContent = 'Editar Subcategoría';
    document.getElementById('subcategoria_id').value = id;
    document.getElementById('subcategoria_nombre').value = nombre;
    document.getElementById('subcategoria_descripcion').value = descripcion || '';
    document.getElementById('subcategoria_activo').checked = activo;
    document.getElementById('subcategoriaModal').classList.remove('hidden');
}

function deleteSubcategoria(id) {
    saveCurrentSection('subcategorias');
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción eliminará la subcategoría",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1f2937',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/subcategorias/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (currentSection) {
                        window.location.href = window.location.pathname + '#' + currentSection;
                        window.location.reload();
                    } else {
                        window.location.reload();
                    }
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'No se pudo eliminar la subcategoría',
                        icon: 'error',
                        confirmButtonColor: '#1f2937'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error al eliminar la subcategoría',
                    icon: 'error',
                    confirmButtonColor: '#1f2937'
                });
            });
        }
    });
}

document.getElementById('subcategoriaForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const id = document.getElementById('subcategoria_id').value;
    const isEdit = id !== '';
    const url = isEdit ? `/subcategorias/${id}` : '/subcategorias';
    const method = isEdit ? 'PUT' : 'POST';

    const formData = {
        categoria_id: categoriaId,
        nombre: document.getElementById('subcategoria_nombre').value,
        descripcion: document.getElementById('subcategoria_descripcion').value,
        activo: document.getElementById('subcategoria_activo').checked ? 1 : 0
    };

    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Error en la respuesta del servidor');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            closeSubcategoriaModal();
            if (currentSection) {
                window.location.href = window.location.pathname + '#' + currentSection;
                window.location.reload();
            } else {
                window.location.reload();
            }
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message || 'No se pudo guardar la subcategoría',
                icon: 'error',
                confirmButtonColor: '#1f2937'
            });
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        Swal.fire({
            title: 'Error',
            text: 'Ocurrió un error al guardar la subcategoría: ' + error.message,
            icon: 'error',
            confirmButtonColor: '#1f2937'
        });
    });
});

// ===== TIPOS INVOLUCRADOS =====
function openTipoInvolucradoModal() {
    saveCurrentSection('tipos-involucrados');
    document.getElementById('tipoInvolucradoModalTitle').textContent = 'Agregar Tipo de Involucrado';
    document.getElementById('tipo_involucrado_id').value = '';
    document.getElementById('tipo_involucrado_nombre').value = '';
    document.getElementById('tipo_involucrado_descripcion').value = '';
    document.getElementById('tipo_involucrado_activo').checked = true;
    document.getElementById('tipoInvolucradoModal').classList.remove('hidden');
}

function closeTipoInvolucradoModal() {
    document.getElementById('tipoInvolucradoModal').classList.add('hidden');
}

function editTipoInvolucrado(id, nombre, descripcion, activo) {
    saveCurrentSection('tipos-involucrados');
    document.getElementById('tipoInvolucradoModalTitle').textContent = 'Editar Tipo de Involucrado';
    document.getElementById('tipo_involucrado_id').value = id;
    document.getElementById('tipo_involucrado_nombre').value = nombre;
    document.getElementById('tipo_involucrado_descripcion').value = descripcion || '';
    document.getElementById('tipo_involucrado_activo').checked = activo;
    document.getElementById('tipoInvolucradoModal').classList.remove('hidden');
}

function deleteTipoInvolucrado(id) {
    saveCurrentSection('tipos-involucrados');
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción eliminará el tipo de involucrado",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1f2937',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/tipos-involucrados/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (currentSection) {
                        window.location.href = window.location.pathname + '#' + currentSection;
                        window.location.reload();
                    } else {
                        window.location.reload();
                    }
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'No se pudo eliminar el tipo de involucrado',
                        icon: 'error',
                        confirmButtonColor: '#1f2937'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error al eliminar el tipo de involucrado',
                    icon: 'error',
                    confirmButtonColor: '#1f2937'
                });
            });
        }
    });
}

document.getElementById('tipoInvolucradoForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const id = document.getElementById('tipo_involucrado_id').value;
    const isEdit = id !== '';
    const url = isEdit ? `/tipos-involucrados/${id}` : '/tipos-involucrados';
    const method = isEdit ? 'PUT' : 'POST';

    const formData = {
        categoria_id: categoriaId,
        nombre: document.getElementById('tipo_involucrado_nombre').value,
        descripcion: document.getElementById('tipo_involucrado_descripcion').value,
        activo: document.getElementById('tipo_involucrado_activo').checked ? 1 : 0
    };

    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Error en la respuesta del servidor');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            closeTipoInvolucradoModal();
            if (currentSection) {
                window.location.href = window.location.pathname + '#' + currentSection;
                window.location.reload();
            } else {
                window.location.reload();
            }
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message || 'No se pudo guardar el tipo de involucrado',
                icon: 'error',
                confirmButtonColor: '#1f2937'
            });
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        Swal.fire({
            title: 'Error',
            text: 'Ocurrió un error al guardar el tipo de involucrado: ' + error.message,
            icon: 'error',
            confirmButtonColor: '#1f2937'
        });
    });
});

// ===== HORARIOS =====
function openHorarioModal() {
    saveCurrentSection('horarios');
    document.getElementById('horarioModalTitle').textContent = 'Agregar Horario';
    document.getElementById('horario_id').value = '';
    document.getElementById('horario_nombre').value = '';
    document.getElementById('horario_descripcion').value = '';
    document.getElementById('horario_activo').checked = true;
    document.getElementById('horarioModal').classList.remove('hidden');
}

function closeHorarioModal() {
    document.getElementById('horarioModal').classList.add('hidden');
}

function editHorario(id, nombre, descripcion, activo) {
    saveCurrentSection('horarios');
    document.getElementById('horarioModalTitle').textContent = 'Editar Horario';
    document.getElementById('horario_id').value = id;
    document.getElementById('horario_nombre').value = nombre;
    document.getElementById('horario_descripcion').value = descripcion || '';
    document.getElementById('horario_activo').checked = activo;
    document.getElementById('horarioModal').classList.remove('hidden');
}

function deleteHorario(id) {
    saveCurrentSection('horarios');
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción eliminará el horario",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1f2937',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/horarios/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (currentSection) {
                        window.location.href = window.location.pathname + '#' + currentSection;
                        window.location.reload();
                    } else {
                        window.location.reload();
                    }
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'No se pudo eliminar el horario',
                        icon: 'error',
                        confirmButtonColor: '#1f2937'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error al eliminar el horario',
                    icon: 'error',
                    confirmButtonColor: '#1f2937'
                });
            });
        }
    });
}

document.getElementById('horarioForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const id = document.getElementById('horario_id').value;
    const isEdit = id !== '';
    const url = isEdit ? `/horarios/${id}` : '/horarios';
    const method = isEdit ? 'PUT' : 'POST';

    const formData = {
        categoria_id: categoriaId,
        nombre: document.getElementById('horario_nombre').value,
        descripcion: document.getElementById('horario_descripcion').value,
        activo: document.getElementById('horario_activo').checked ? 1 : 0
    };

    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Error en la respuesta del servidor');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            closeHorarioModal();
            if (currentSection) {
                window.location.href = window.location.pathname + '#' + currentSection;
                window.location.reload();
            } else {
                window.location.reload();
            }
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message || 'No se pudo guardar el horario',
                icon: 'error',
                confirmButtonColor: '#1f2937'
            });
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        Swal.fire({
            title: 'Error',
            text: 'Ocurrió un error al guardar el horario: ' + error.message,
            icon: 'error',
            confirmButtonColor: '#1f2937'
        });
    });
});

// ===== ACCIONES =====
function openAccionModal() {
    saveCurrentSection('acciones');
    document.getElementById('accionModalTitle').textContent = 'Agregar Acción';
    document.getElementById('accion_id').value = '';
    document.getElementById('accion_nombre').value = '';
    document.getElementById('accion_descripcion').value = '';
    document.getElementById('accion_activo').checked = true;
    document.getElementById('accionModal').classList.remove('hidden');
}

function closeAccionModal() {
    document.getElementById('accionModal').classList.add('hidden');
}

function editAccion(id, nombre, descripcion, activo) {
    saveCurrentSection('acciones');
    document.getElementById('accionModalTitle').textContent = 'Editar Acción';
    document.getElementById('accion_id').value = id;
    document.getElementById('accion_nombre').value = nombre;
    document.getElementById('accion_descripcion').value = descripcion || '';
    document.getElementById('accion_activo').checked = activo;
    document.getElementById('accionModal').classList.remove('hidden');
}

function deleteAccion(id) {
    saveCurrentSection('acciones');
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción eliminará la acción",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1f2937',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/acciones/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (currentSection) {
                        window.location.href = window.location.pathname + '#' + currentSection;
                        window.location.reload();
                    } else {
                        window.location.reload();
                    }
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'No se pudo eliminar la acción',
                        icon: 'error',
                        confirmButtonColor: '#1f2937'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error al eliminar la acción',
                    icon: 'error',
                    confirmButtonColor: '#1f2937'
                });
            });
        }
    });
}

document.getElementById('accionForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const accionId = document.getElementById('accion_id').value;
    const isEdit = accionId !== '';

    const formData = {
        categoria_id: categoriaId,
        nombre: document.getElementById('accion_nombre').value,
        descripcion: document.getElementById('accion_descripcion').value,
        activo: document.getElementById('accion_activo').checked ? 1 : 0
    };

    const url = isEdit ? `/acciones/${accionId}` : '/acciones';
    const method = isEdit ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Error en la respuesta del servidor');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            closeAccionModal();
            if (currentSection) {
                window.location.href = window.location.pathname + '#' + currentSection;
                window.location.reload();
            } else {
                window.location.reload();
            }
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message || 'No se pudo guardar la acción',
                icon: 'error',
                confirmButtonColor: '#1f2937'
            });
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        Swal.fire({
            title: 'Error',
            text: 'Ocurrió un error al guardar la acción: ' + error.message,
            icon: 'error',
            confirmButtonColor: '#1f2937'
        });
    });
});

// ===== DESENLACES =====
function openDesenlaceModal() {
    saveCurrentSection('desenlaces');
    document.getElementById('desenlaceModalTitle').textContent = 'Agregar Desenlace';
    document.getElementById('desenlace_id').value = '';
    document.getElementById('desenlace_nombre').value = '';
    document.getElementById('desenlace_descripcion').value = '';
    document.getElementById('desenlace_activo').checked = true;
    document.getElementById('desenlaceModal').classList.remove('hidden');
}

function closeDesenlaceModal() {
    document.getElementById('desenlaceModal').classList.add('hidden');
}

function editDesenlace(id, nombre, descripcion, activo) {
    saveCurrentSection('desenlaces');
    document.getElementById('desenlaceModalTitle').textContent = 'Editar Desenlace';
    document.getElementById('desenlace_id').value = id;
    document.getElementById('desenlace_nombre').value = nombre;
    document.getElementById('desenlace_descripcion').value = descripcion || '';
    document.getElementById('desenlace_activo').checked = activo;
    document.getElementById('desenlaceModal').classList.remove('hidden');
}

function deleteDesenlace(id) {
    saveCurrentSection('desenlaces');
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción eliminará el desenlace",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1f2937',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/desenlaces/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (currentSection) {
                        window.location.href = window.location.pathname + '#' + currentSection;
                        window.location.reload();
                    } else {
                        window.location.reload();
                    }
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'No se pudo eliminar el desenlace',
                        icon: 'error',
                        confirmButtonColor: '#1f2937'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error al eliminar el desenlace',
                    icon: 'error',
                    confirmButtonColor: '#1f2937'
                });
            });
        }
    });
}

document.getElementById('desenlaceForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const desenlaceId = document.getElementById('desenlace_id').value;
    const isEdit = desenlaceId !== '';

    const formData = {
        categoria_id: categoriaId,
        nombre: document.getElementById('desenlace_nombre').value,
        descripcion: document.getElementById('desenlace_descripcion').value,
        activo: document.getElementById('desenlace_activo').checked ? 1 : 0
    };

    const url = isEdit ? `/desenlaces/${desenlaceId}` : '/desenlaces';
    const method = isEdit ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Error en la respuesta del servidor');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            closeDesenlaceModal();
            if (currentSection) {
                window.location.href = window.location.pathname + '#' + currentSection;
                window.location.reload();
            } else {
                window.location.reload();
            }
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message || 'No se pudo guardar el desenlace',
                icon: 'error',
                confirmButtonColor: '#1f2937'
            });
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        Swal.fire({
            title: 'Error',
            text: 'Ocurrió un error al guardar el desenlace: ' + error.message,
            icon: 'error',
            confirmButtonColor: '#1f2937'
        });
    });
});
