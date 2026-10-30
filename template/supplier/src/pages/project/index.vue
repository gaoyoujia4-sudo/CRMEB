<template>
  <div class="page">
    <div class="header">
      <div class="header-title">研学供应商 · 我的研学项目</div>
      <div class="header-right">
        <span class="welcome">欢迎，{{ supplierName }}</span>
        <el-button type="text" @click="logout">退出登录</el-button>
      </div>
    </div>

    <div class="content">
      <el-table :data="list" v-loading="loading" border stripe>
        <el-table-column label="封面图" width="90">
          <template slot-scope="scope">
            <img v-if="scope.row.image" :src="scope.row.image" class="cover" />
            <span v-else>-</span>
          </template>
        </el-table-column>
        <el-table-column label="项目名称" prop="title" min-width="200" show-overflow-tooltip></el-table-column>
        <el-table-column label="价格类型" width="110">
          <template slot-scope="scope">
            <el-tag size="small" :type="scope.row.price_type == 1 ? 'primary' : 'warning'">
              {{ scope.row.price_type == 1 ? '按人计价' : '按团计价' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="价格" prop="price" width="110"></el-table-column>
        <el-table-column label="划线价" prop="ot_price" width="110"></el-table-column>
        <el-table-column label="讲师" prop="lecturer_name" min-width="160" show-overflow-tooltip></el-table-column>
        <el-table-column label="状态" width="90">
          <template slot-scope="scope">
            <el-tag size="small" :type="scope.row.status == 1 ? 'success' : 'info'">
              {{ scope.row.status == 1 ? '启用' : '禁用' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="90" fixed="right">
          <template slot-scope="scope">
            <el-button type="text" @click="showDetail(scope.row)">详情</el-button>
          </template>
        </el-table-column>
      </el-table>

      <div class="pager">
        <el-pagination
          background
          layout="total, prev, pager, next"
          :total="total"
          :current-page="page"
          :page-size="limit"
          @current-change="handlePage"
        ></el-pagination>
      </div>
    </div>

    <el-dialog title="项目详情" :visible.sync="detailVisible" width="560px">
      <div v-if="info" class="detail">
        <div class="detail-row">
          <span class="label">封面图：</span>
          <img v-if="info.image" :src="info.image" class="detail-cover" />
          <span v-else>-</span>
        </div>
        <div class="detail-row"><span class="label">项目名称：</span>{{ info.title }}</div>
        <div class="detail-row"><span class="label">价格类型：</span>{{ info.price_type == 1 ? '按人计价' : '按团计价' }}</div>
        <div class="detail-row"><span class="label">价格：</span>{{ info.price }}</div>
        <div class="detail-row"><span class="label">划线原价：</span>{{ info.ot_price }}</div>
        <div class="detail-row"><span class="label">讲师：</span>{{ lecturerNames }}</div>
        <div class="detail-row"><span class="label">服务商：</span>{{ info.provider_name || '-' }}</div>
        <div class="detail-row"><span class="label">状态：</span>{{ info.status == 1 ? '启用' : '禁用' }}</div>
        <div class="detail-row"><span class="label">项目介绍：</span></div>
        <div class="detail-intro">{{ info.intro || '暂无介绍' }}</div>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { projectList, projectDetail } from '@/api/study';
import { supplierInfo, supplierLogout } from '@/api/login';
import { getSupplierInfo, removeToken } from '@/libs/util';

export default {
  name: 'project',
  data() {
    return {
      loading: false,
      list: [],
      total: 0,
      page: 1,
      limit: 20,
      supplierName: '',
      detailVisible: false,
      info: null,
    };
  },
  computed: {
    lecturerNames() {
      if (!this.info) return '-';
      const lecturers = this.info.lecturers || [];
      return lecturers.length ? lecturers.map((i) => i.name).join('、') : '-';
    },
  },
  mounted() {
    const cached = getSupplierInfo();
    this.supplierName = cached.real_name || cached.account || '供应商';
    this.getInfo();
    this.getList();
  },
  methods: {
    // 拉取当前供应商信息（校验登录态）
    getInfo() {
      supplierInfo()
        .then((res) => {
          const info = res.data || {};
          this.supplierName = info.real_name || info.account || '供应商';
        })
        .catch(() => {});
    },
    getList() {
      this.loading = true;
      projectList({ page: this.page, limit: this.limit })
        .then((res) => {
          const data = res.data || {};
          this.list = data.list || [];
          this.total = data.count || 0;
        })
        .catch(() => {})
        .finally(() => {
          this.loading = false;
        });
    },
    handlePage(page) {
      this.page = page;
      this.getList();
    },
    showDetail(row) {
      projectDetail(row.id)
        .then((res) => {
          this.info = res.data.info || null;
          this.detailVisible = true;
        })
        .catch(() => {});
    },
    logout() {
      supplierLogout()
        .catch(() => {})
        .finally(() => {
          removeToken();
          this.$router.replace({ name: 'login' });
        });
    },
  },
};
</script>
<style scoped>
.page {
  min-height: 100%;
}
.header {
  height: 56px;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 24px;
  box-shadow: 0 1px 4px rgba(0, 21, 41, 0.08);
}
.header-title {
  font-size: 16px;
  font-weight: 600;
  color: #303133;
}
.header-right {
  display: flex;
  align-items: center;
}
.welcome {
  margin-right: 12px;
  color: #606266;
}
.content {
  margin: 16px;
  background: #fff;
  padding: 16px;
  border-radius: 4px;
}
.cover {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 4px;
}
.pager {
  margin-top: 16px;
  text-align: right;
}
.detail {
  font-size: 14px;
  color: #303133;
}
.detail-row {
  margin-bottom: 10px;
}
.label {
  color: #909399;
}
.detail-cover {
  width: 160px;
  height: 100px;
  object-fit: cover;
  border-radius: 4px;
  vertical-align: middle;
}
.detail-intro {
  line-height: 1.6;
  color: #606266;
  white-space: pre-wrap;
}
</style>
