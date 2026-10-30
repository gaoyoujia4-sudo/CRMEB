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
          <el-form-item label="场次名称：">
            <el-input
              clearable
              placeholder="请输入场次名称"
              v-model="fromData.keywords"
              class="form_content_width"
              @keyup.enter.native="userSearchs"
            />
          </el-form-item>
          <el-form-item label="状态：">
            <el-select v-model="fromData.status" placeholder="请选择" clearable class="form_content_width" @change="userSearchs">
              <el-option label="全部" value="" />
              <el-option label="开启" :value="1" />
              <el-option label="关闭" :value="0" />
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
      <el-button type="primary" v-db-click @click="add">添加抢购场次</el-button>

      <el-table
        :data="snatchList"
        ref="table"
        class="mt14"
        v-loading="loading"
        highlight-current-row
      >
        <el-table-column label="ID" prop="id" width="70">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="场次名称" prop="title" min-width="140">
          <template slot-scope="scope">
            <span>{{ scope.row.title }}</span>
          </template>
        </el-table-column>
        <el-table-column label="研学商品" prop="store_name" min-width="160">
          <template slot-scope="scope">
            <span>{{ scope.row.store_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="开抢时间" min-width="160">
          <template slot-scope="scope">
            <span>{{ timeFormat(scope.row.start_time) }}</span>
          </template>
        </el-table-column>
        <el-table-column label="结束时间" min-width="160">
          <template slot-scope="scope">
            <span>{{ timeFormat(scope.row.stop_time) }}</span>
          </template>
        </el-table-column>
        <el-table-column label="抢购价" prop="price" min-width="90">
          <template slot-scope="scope">
            <span>{{ scope.row.price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="原价" prop="ot_price" min-width="90">
          <template slot-scope="scope">
            <span>{{ scope.row.ot_price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="已抢/总名额" min-width="110">
          <template slot-scope="scope">
            <span>{{ scope.row.sales }} / {{ scope.row.quota_show }}</span>
          </template>
        </el-table-column>
        <el-table-column label="每人限购" prop="num" min-width="90">
          <template slot-scope="scope">
            <span>{{ scope.row.num }}</span>
          </template>
        </el-table-column>
        <el-table-column label="单次限购" prop="once_num" min-width="90">
          <template slot-scope="scope">
            <span>{{ scope.row.once_num }}</span>
          </template>
        </el-table-column>
        <el-table-column label="排序" prop="sort" min-width="70">
          <template slot-scope="scope">
            <span>{{ scope.row.sort }}</span>
          </template>
        </el-table-column>
        <el-table-column label="状态" prop="status" min-width="120">
          <template slot-scope="scope">
            <el-switch
              class="defineSwitch"
              :active-value="1"
              :inactive-value="0"
              v-model="scope.row.status"
              :value="scope.row.status"
              @change="onchangeStatus(scope.row)"
              size="large"
              active-text="开启"
              inactive-text="关闭"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="120" fixed="right">
          <template slot-scope="scope">
            <a v-db-click @click="edit(scope.row)">编辑</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row)">删除</a>
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

    <el-dialog :visible.sync="modals" closable :title="isEdit ? '编辑抢购场次' : '添加抢购场次'" width="660" @close="cancel">
      <el-form label-position="right" size="small" ref="form" :rules="rules" :model="form" label-width="100px">
        <el-form-item label="研学商品：" prop="product_id">
          <el-input v-model="form.store_name" class="w-420" placeholder="请选择研学商品" readonly>
            <el-button slot="append" @click="chooseProduct">选择商品</el-button>
          </el-input>
        </el-form-item>
        <el-form-item label="场次名称：" prop="title">
          <el-input v-model="form.title" class="w-420" placeholder="如：第1期"></el-input>
        </el-form-item>
        <el-form-item label="开抢时间：" prop="start_time">
          <el-date-picker
            v-model="form.start_time"
            type="datetime"
            value-format="yyyy-MM-dd HH:mm:ss"
            placeholder="请选择开抢时间"
            class="w-420"
          ></el-date-picker>
        </el-form-item>
        <el-form-item label="结束时间：" prop="stop_time">
          <el-date-picker
            v-model="form.stop_time"
            type="datetime"
            value-format="yyyy-MM-dd HH:mm:ss"
            placeholder="请选择结束时间"
            class="w-420"
          ></el-date-picker>
        </el-form-item>
        <el-form-item label="抢购价：" prop="price">
          <el-input-number v-model="form.price" :min="0" :precision="2" :max="999999" class="w-420"></el-input-number>
        </el-form-item>
        <el-form-item label="划线原价：" prop="ot_price">
          <el-input-number v-model="form.ot_price" :min="0" :precision="2" :max="999999" class="w-420"></el-input-number>
        </el-form-item>
        <el-form-item label="总名额：" prop="quota_show">
          <el-input-number v-model="form.quota_show" :min="1" :max="999999" :disabled="isEdit" class="w-420"></el-input-number>
          <span v-if="isEdit" class="ivu-ml-8">（已保存场次不允许修改总名额）</span>
        </el-form-item>
        <el-form-item label="每人限购：" prop="num">
          <el-input-number v-model="form.num" :min="1" :max="999" class="w-420"></el-input-number>
        </el-form-item>
        <el-form-item label="单次限购：" prop="once_num">
          <el-input-number v-model="form.once_num" :min="1" :max="999" class="w-420"></el-input-number>
        </el-form-item>
        <el-form-item label="排序：">
          <el-input-number v-model="form.sort" :min="0" :max="999" class="w-420"></el-input-number>
        </el-form-item>
        <el-form-item label="集合地点：">
          <el-input v-model="form.meeting_point_name" class="w-420" placeholder="如：大雁塔北广场东门" maxlength="100"></el-input>
        </el-form-item>
        <el-form-item label="详细地址：">
          <el-input v-model="form.meeting_point_address" class="w-420" placeholder="如：陕西省西安市雁塔区大雁塔北广场东门内" maxlength="255"></el-input>
        </el-form-item>
        <el-form-item label="经度：">
          <el-input v-model="form.meeting_point_lng" class="w-420" placeholder="如：108.969855（用于小程序一键导航，可空）" maxlength="32"></el-input>
        </el-form-item>
        <el-form-item label="纬度：">
          <el-input v-model="form.meeting_point_lat" class="w-420" placeholder="如：34.218596（用于小程序一键导航，可空）" maxlength="32"></el-input>
        </el-form-item>
        <el-form-item label="是否开启：">
          <el-switch v-model="form.status" :active-value="1" :inactive-value="0" size="large">
            <span slot="open">开启</span>
            <span slot="close">关闭</span>
          </el-switch>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="cancel">取 消</el-button>
        <el-button type="primary" v-db-click @click="saveConfirm">确 定</el-button>
      </span>
    </el-dialog>

    <!-- 选择商品 -->
    <el-dialog :visible.sync="goodsModals" title="选择研学商品" class="paymentFooter" width="1000px">
      <goods-list ref="goodslist" :cate-id="[41]" @getProductId="getProductId"></goods-list>
    </el-dialog>
  </div>
</template>
<script>
import { mapState } from 'vuex';
import goodsList from '@/components/goodsList/index';
import {
  storeSnatchListApi,
  storeSnatchInfoApi,
  storeSnatchSaveApi,
  storeSnatchDelApi,
} from '@/api/study';
export default {
  name: 'storeSnatch',
  components: {
    goodsList,
  },
  data() {
    return {
      loading: false,
      fromData: {
        page: 1,
        limit: 15,
        keywords: '',
        status: '',
      },
      snatchList: [],
      total: 0,
      modals: false,
      goodsModals: false,
      isEdit: false,
      form: {
        id: 0,
        product_id: 0,
        store_name: '',
        title: '',
        start_time: '',
        stop_time: '',
        price: 0,
        ot_price: 0,
        quota_show: 0,
        num: 1,
        once_num: 1,
        status: 1,
        sort: 0,
        meeting_point_name: '',
        meeting_point_address: '',
        meeting_point_lng: '',
        meeting_point_lat: '',
      },
      rules: {
        product_id: [{ required: true, message: '请选择研学商品', trigger: 'change' }],
        title: [{ required: true, message: '请输入场次名称', trigger: 'blur' }],
        start_time: [{ required: true, message: '请选择开抢时间', trigger: 'change' }],
        stop_time: [{ required: true, message: '请选择结束时间', trigger: 'change' }],
        price: [{ required: true, message: '请输入抢购价', trigger: 'blur' }],
        quota_show: [
          { required: true, message: '请输入总名额', trigger: 'blur' },
          { type: 'number', min: 1, message: '总名额必须大于0', trigger: 'blur' },
        ],
      },
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
    // 时间戳格式化
    timeFormat(ts) {
      if (!ts) return '';
      let d = new Date(ts * 1000);
      let pad = (n) => (n < 10 ? '0' + n : '' + n);
      return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
    },
    // 添加
    add() {
      this.modals = true;
      this.isEdit = false;
    },
    // 选择商品弹窗
    chooseProduct() {
      this.goodsModals = true;
    },
    // 商品选择回调
    getProductId(row) {
      this.goodsModals = false;
      this.form.product_id = row.id;
      this.form.store_name = row.store_name;
      // 自动带出商品价格：抢购价默认取商品现价，划线原价取商品原价（可手动改）
      this.form.price = Number(row.price || 0);
      this.form.ot_price = Number(row.ot_price || row.price || 0);
    },
    // 编辑
    edit(row) {
      storeSnatchInfoApi(row.id)
        .then((res) => {
          let info = res.data.info;
          this.form = {
            id: info.id,
            product_id: info.product_id,
            store_name: info.store_name || row.store_name || '',
            title: info.title,
            start_time: this.timeFormat(info.start_time),
            stop_time: this.timeFormat(info.stop_time),
            price: Number(info.price),
            ot_price: Number(info.ot_price),
            quota_show: Number(info.quota_show),
            num: Number(info.num),
            once_num: Number(info.once_num),
            status: info.status,
            sort: Number(info.sort),
            meeting_point_name: info.meeting_point_name || '',
            meeting_point_address: info.meeting_point_address || '',
            meeting_point_lng: info.meeting_point_lng || '',
            meeting_point_lat: info.meeting_point_lat || '',
          };
          this.isEdit = true;
          this.modals = true;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // 删除
    del(row) {
      let delfromData = {
        title: '删除抢购场次',
        num: 0,
        url: `study/snatch/del/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // 列表
    getList() {
      this.loading = true;
      storeSnatchListApi(this.fromData)
        .then(async (res) => {
          let data = res.data;
          this.snatchList = data.list;
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
    // 修改状态
    onchangeStatus(row) {
      storeSnatchSaveApi(row.id, {
        product_id: row.product_id,
        title: row.title,
        start_time: row.start_time,
        stop_time: row.stop_time,
        price: row.price,
        ot_price: row.ot_price,
        quota_show: row.quota_show,
        num: row.num,
        once_num: row.once_num,
        status: row.status,
        sort: row.sort,
        meeting_point_name: row.meeting_point_name || '',
        meeting_point_address: row.meeting_point_address || '',
        meeting_point_lng: row.meeting_point_lng || '',
        meeting_point_lat: row.meeting_point_lat || '',
      })
        .then(async (res) => {
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // 确认保存
    saveConfirm() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          let data = { ...this.form };
          if (data.num < data.once_num) {
            return this.$message.error('每人限购数量不能小于单次限购数量');
          }
          storeSnatchSaveApi(data.id, data)
            .then((res) => {
              this.$message.success(res.msg);
              this.modals = false;
              this.cancel();
              this.fromData.page = 1;
              this.getList();
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        }
      });
    },
    // 取消
    cancel() {
      this.form = {
        id: 0,
        product_id: 0,
        store_name: '',
        title: '',
        start_time: '',
        stop_time: '',
        price: 0,
        ot_price: 0,
        quota_show: 0,
        num: 1,
        once_num: 1,
        status: 1,
        sort: 0,
        meeting_point_name: '',
        meeting_point_address: '',
        meeting_point_lng: '',
        meeting_point_lat: '',
      };
      this.modals = false;
    },
  },
};
</script>

<style lang="scss" scoped>
.w-420 {
  width: 420px;
}
</style>
