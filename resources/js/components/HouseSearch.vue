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

    <form class="card" @submit.prevent="submit">
      <div class="grid">
        <label>
          Name
          <input v-model="form.name" type="text" placeholder="e.g. Victoria" />
        </label>
        <label>
          Bedrooms
          <input v-model.number="form.bedrooms" type="number" min="0" />
        </label>
        <label>
          Bathrooms
          <input v-model.number="form.bathrooms" type="number" min="0" />
        </label>
        <label>
          Storeys
          <input v-model.number="form.storeys" type="number" min="0" />
        </label>
        <label>
          Garages
          <input v-model.number="form.garages" type="number" min="0" />
        </label>
        <label>
          Price From
          <input v-model.number="form.price_from" type="number" min="0" />
        </label>
        <label>
          Price To
          <input v-model.number="form.price_to" type="number" min="0" />
        </label>
      </div>

      <div class="actions">
        <button type="submit" :disabled="isLoading">Search</button>
        <button type="button" class="secondary" @click="reset" :disabled="isLoading">Reset</button>
        <div class="loader" v-if="isLoading">
          <span class="dot"></span>
          <span class="dot"></span>
          <span class="dot"></span>
        </div>
      </div>
    </form>

    <div class="card table-card">
      <table v-if="rows.length">
        <thead>
          <tr>
            <th>Name</th>
            <th>Bedrooms</th>
            <th>Bathrooms</th>
            <th>Storeys</th>
            <th>Garages</th>
            <th>Price</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in rows" :key="row.id">
            <td>{{ row.name }}</td>
            <td>{{ row.bedrooms }}</td>
            <td>{{ row.bathrooms }}</td>
            <td>{{ row.storeys }}</td>
            <td>{{ row.garages }}</td>
            <td>{{ formatPrice(row.price) }}</td>
          </tr>
        </tbody>
      </table>
      <div v-else class="empty">
        No results found
      </div>
    </div>

    <div class="pagination" v-if="meta.last_page > 1">
      <button class="secondary" :disabled="isLoading || meta.current_page <= 1" @click="go(meta.current_page - 1)">Prev</button>
      <button class="secondary" :disabled="isLoading || meta.current_page >= meta.last_page" @click="go(meta.current_page + 1)">Next</button>
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
    formatPrice(value) {
      if (value === null || value === undefined) return '';
      return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(value);
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
  padding: 24px;
  backdrop-filter: blur(6px);
}

.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 16px;
}

label {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 13px;
  color: #cbd5f5;
}

input {
  background: rgba(15, 23, 42, 0.9);
  border: 1px solid rgba(148, 163, 184, 0.35);
  color: #e2e8f0;
  padding: 8px 10px;
  border-radius: 8px;
}

.actions {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 18px;
}

button {
  background: #38bdf8;
  color: #0f172a;
  border: none;
  border-radius: 8px;
  padding: 10px 16px;
  cursor: pointer;
  font-weight: 600;
}

button.secondary {
  background: transparent;
  border: 1px solid rgba(148, 163, 184, 0.5);
  color: #e2e8f0;
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.table-card table {
  width: 100%;
  border-collapse: collapse;
}

.table-card th,
.table-card td {
  padding: 12px 10px;
  text-align: left;
  border-bottom: 1px solid rgba(148, 163, 184, 0.2);
  font-size: 14px;
}

.empty {
  text-align: center;
  padding: 32px 12px;
  color: #94a3b8;
}

.loader {
  display: inline-flex;
  gap: 6px;
  align-items: center;
  margin-left: auto;
}

.dot {
  width: 6px;
  height: 6px;
  background: #38bdf8;
  border-radius: 999px;
  animation: pulse 0.9s infinite ease-in-out;
}

.dot:nth-child(2) {
  animation-delay: 0.15s;
}

.dot:nth-child(3) {
  animation-delay: 0.3s;
}

@keyframes pulse {
  0%,
  100% {
    transform: scale(0.6);
    opacity: 0.5;
  }
  50% {
    transform: scale(1);
    opacity: 1;
  }
}

.pagination {
  max-width: 1100px;
  margin: 0 auto;
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

@media (max-width: 720px) {
  .header {
    flex-direction: column;
    align-items: flex-start;
  }
  .actions {
    flex-wrap: wrap;
  }
  .loader {
    width: 100%;
    justify-content: flex-start;
  }
}
</style>
