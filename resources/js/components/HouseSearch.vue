<template>
  <div class="page">
    <div class="header">
      <div>
        <h1>House Search</h1>
        <p>Filter by name, rooms, and price range.</p>
      </div>
      <div class="meta">
        <span>Results: {{ meta.total }}</span>
        <span v-if="meta.last_page > 1">Page {{ meta.current_page }} of {{ meta.last_page }}</span>
      </div>
    </div>

    <el-card class="card" shadow="never">
      <el-form :model="form" label-position="top" @submit.prevent>
        <div class="grid">
          <el-form-item label="Name">
            <el-input v-model="form.name" placeholder="e.g. Victoria" clearable />
          </el-form-item>
          <el-form-item label="Bedrooms">
            <el-input-number v-model="form.bedrooms" :min="0" controls-position="right" />
          </el-form-item>
          <el-form-item label="Bathrooms">
            <el-input-number v-model="form.bathrooms" :min="0" controls-position="right" />
          </el-form-item>
          <el-form-item label="Storeys">
            <el-input-number v-model="form.storeys" :min="0" controls-position="right" />
          </el-form-item>
          <el-form-item label="Garages">
            <el-input-number v-model="form.garages" :min="0" controls-position="right" />
          </el-form-item>
          <el-form-item label="Price From">
            <el-input-number v-model="form.price_from" :min="0" controls-position="right" />
          </el-form-item>
          <el-form-item label="Price To">
            <el-input-number v-model="form.price_to" :min="0" controls-position="right" />
          </el-form-item>
        </div>

        <div class="actions">
          <el-button type="primary" :loading="isLoading" @click="submit">Search</el-button>
          <el-button :disabled="isLoading" @click="reset">Reset</el-button>
        </div>
      </el-form>
    </el-card>

    <el-card class="card" shadow="never">
      <el-table v-if="rows.length" :data="rows" stripe>
        <el-table-column prop="name" label="Name" />
        <el-table-column prop="bedrooms" label="Bedrooms" />
        <el-table-column prop="bathrooms" label="Bathrooms" />
        <el-table-column prop="storeys" label="Storeys" />
        <el-table-column prop="garages" label="Garages" />
        <el-table-column prop="price" label="Price" :formatter="priceFormatter" />
      </el-table>
      <div v-else class="empty">No results found</div>
    </el-card>

    <div class="pagination" v-if="meta.last_page > 1">
      <el-button :disabled="isLoading || meta.current_page <= 1" @click="go(meta.current_page - 1)">Prev</el-button>
      <el-button :disabled="isLoading || meta.current_page >= meta.last_page" @click="go(meta.current_page + 1)">Next</el-button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      form: {
        name: '',
        bedrooms: null,
        bathrooms: null,
        storeys: null,
        garages: null,
        price_from: null,
        price_to: null,
        page: 1,
        per_page: 20,
      },
      rows: [],
      meta: {
        current_page: 1,
        per_page: 20,
        total: 0,
        last_page: 1,
      },
      isLoading: false,
    };
  },
  mounted() {
    this.submit();
  },
  methods: {
    async submit() {
      this.isLoading = true;
      try {
        const params = this.cleanParams();
        const { data } = await axios.get('/api/houses', { params });
        this.rows = data.data || [];
        this.meta = data.meta || this.meta;
      } finally {
        this.isLoading = false;
      }
    },
    reset() {
      this.form = {
        name: '',
        bedrooms: null,
        bathrooms: null,
        storeys: null,
        garages: null,
        price_from: null,
        price_to: null,
        page: 1,
        per_page: 20,
      };
      this.submit();
    },
    go(page) {
      this.form.page = page;
      this.submit();
    },
    cleanParams() {
      const params = {};
      Object.entries(this.form).forEach(([key, value]) => {
        if (value !== null && value !== '' && value !== undefined) {
          params[key] = value;
        }
      });
      return params;
    },
    priceFormatter(row, column, cellValue) {
      if (cellValue === null || cellValue === undefined) return '';
      return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(cellValue);
    },
  },
};
</script>

<style scoped>
.page {
  min-height: 100vh;
  padding: 48px 20px 64px;
  background: radial-gradient(circle at top right, #1f2937 0%, #0f172a 40%, #0b1020 100%);
  color: #e2e8f0;
  font-family: "Space Grotesk", "Segoe UI", sans-serif;
}

.header {
  max-width: 1100px;
  margin: 0 auto 24px;
  display: flex;
  justify-content: space-between;
  gap: 24px;
  align-items: flex-end;
}

.header h1 {
  font-size: 32px;
  margin: 0 0 8px;
}

.header p {
  margin: 0;
  color: #94a3b8;
}

.meta {
  display: flex;
  flex-direction: column;
  gap: 4px;
  color: #94a3b8;
  font-size: 14px;
}

.card {
  max-width: 1100px;
  margin: 0 auto 24px;
  background: rgba(15, 23, 42, 0.7);
  border: 1px solid rgba(148, 163, 184, 0.2);
  border-radius: 16px;
  padding: 8px 8px 4px;
  backdrop-filter: blur(6px);
}

.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 12px;
}

.actions {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 12px 0 8px;
}

.empty {
  text-align: center;
  padding: 32px 12px;
  color: #94a3b8;
}

.pagination {
  max-width: 1100px;
  margin: 0 auto;
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

:deep(.el-form-item__label) {
  color: #cbd5f5;
  font-weight: 500;
}

:deep(.el-input__wrapper),
:deep(.el-input-number .el-input__wrapper),
:deep(.el-select__wrapper) {
  background-color: rgba(15, 23, 42, 0.9);
  border: 1px solid rgba(148, 163, 184, 0.35);
  box-shadow: none;
}

:deep(.el-input__inner) {
  color: #e2e8f0;
}

:deep(.el-table) {
  background-color: transparent;
}

:deep(.el-table th.el-table__cell) {
  background-color: rgba(30, 41, 59, 0.6);
  color: #e2e8f0;
}

:deep(.el-table tr) {
  background-color: rgba(15, 23, 42, 0.2);
}

@media (max-width: 720px) {
  .header {
    flex-direction: column;
    align-items: flex-start;
  }
  .actions {
    flex-wrap: wrap;
  }
}
</style>
