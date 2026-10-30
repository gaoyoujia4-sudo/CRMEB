<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mb-16" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="formValidate"
          :model="fromData"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
          inline
        >
          <el-form-item label="关键词：">
            <el-input
              clearable
              placeholder="订单号 / 姓名 / 手机号 / 身份证号"
              v-model="fromData.keywords"
              class="form_content_width"
              @keyup.enter.native="userSearchs"
            />
          </el-form-item>
          <el-form-item label="订单状态：">
            <el-select v-model="fromData.status" placeholder="请选择" clearable class="form_content_width" @change="userSearchs">
              <el-option label="全部" value="" />
              <el-option label="待核销" :value="0" />
              <el-option label="已核销" :value="2" />
              <el-option label="已完成" :value="3" />
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="userSearchs">查询</el-button>
            <el-button v-db-click @click="reset">重置</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never">
      <el-table
        :data="buyerList"
        ref="table"
        class="mt14"
        v-loading="loading"
        highlight-current-row
      >
        <el-table-column label="订单号" prop="order_id" min-width="180">
          <template slot-scope="scope">
            <span>{{ scope.row.order_id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="抢购人姓名" prop="real_name" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.real_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="手机号" prop="user_phone" min-width="120">
          <template slot-scope="scope">
            <span>{{ scope.row.user_phone }}</span>
          </template>
        </el-table-column>
        <el-table-column label="身份证号" prop="user_id_card" min-width="170">
          <template slot-scope="scope">
            <span>{{ scope.row.user_id_card }}</span>
          </template>
        </el-table-column>
        <el-table-column label="商品名称" prop="store_name" min-width="150">
          <template slot-scope="scope">
            <span>{{ scope.row.store_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="套餐类型" prop="suk" min-width="120">
          <template slot-scope="scope">
            <span>{{ scope.row.suk }}</span>
          </template>
        </el-table-column>
        <el-table-column label="场次" prop="snatch_title" min-width="110">
          <template slot-scope="scope">
            <span>{{ scope.row.snatch_title }}</span>
          </template>
        </el-table-column>
        <el-table-column label="研学套餐" prop="study_package_title" min-width="160">
          <template slot-scope="scope">
            <span>{{ scope.row.study_package_title || '—' }}</span>
          </template>
        </el-table-column>
        <el-table-column label="出行日期" prop="study_travel_date" min-width="110">
          <template slot-scope="scope">
            <span>{{ scope.row.study_travel_date || '—' }}</span>
          </template>
        </el-table-column>
        <el-table-column label="讲师" prop="lecturer_name" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.lecturer_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="实付金额" prop="pay_price" min-width="90">
          <template slot-scope="scope">
            <span>￥{{ scope.row.pay_price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="下单时间" min-width="160">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time_text }}</span>
          </template>
        </el-table-column>
        <el-table-column label="核销码" prop="verify_code" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.verify_code || '—' }}</span>
          </template>
        </el-table-column>
        <el-table-column label="订单状态" min-width="100">
          <template slot-scope="scope">
            <span>{{ statusText(scope.row.status) }}</span>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="90" fixed="right">
          <template slot-scope="scope">
            <a
              v-if="scope.row.verify_code && scope.row.status < 2"
              v-db-click
              @click="writeOff(scope.row)"
            >核销</a>
            <span v-else style="color: #c0c4cc">核销</span>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="fromData.page"
          :limit.sync="fromData.limit"
          @pagination="getList"
        />
      </div>
    </el-card>
  </div>
</template>
<script>
import { mapState } from 'vuex';
import { studyOrderListApi } from '@/api/study';
import { putWrite } from '@/api/order';
export default {
  name: 'studyOrder',
  data() {
    return {
      loading: false,
      fromData: {
        page: 1,
        limit: 15,
        keywords: '',
        status: '',
      },
      buyerList: [],
      total: 0,
    };
  },
  computed: {
    ...mapState('media', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '90px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  mounted() {
    this.getList();
  },
  methods: {
    // 订单状态文案（研学订单：付款后待核销，核销后已核销/已完成）
    statusText(status) {
      let map = {
        0: '待核销',
        2: '已核销',
        3: '已完成',
      };
      return map[status] !== undefined ? map[status] : status;
    },
    // 列表
    getList() {
      this.loading = true;
      studyOrderListApi(this.fromData)
        .then(async (res) => {
          let data = res.data;
          this.buyerList = data.list;
          this.total = data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // 搜索
    userSearchs() {
      this.fromData.page = 1;
      this.getList();
    },
    // 重置
    reset() {
      this.fromData.keywords = '';
      this.fromData.status = '';
      this.fromData.page = 1;
      this.getList();
    },
    // 核销
    writeOff(row) {
      if (!row.verify_code) return;
      let data = {
        title: '核销',
        success: putWrite({ code: row.verify_code, confirm: 1 }),
      };
      this.$modalSure(data)
        .then((res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
  },
};
</script>
