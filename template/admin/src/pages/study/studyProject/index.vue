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
          <el-form-item label="项目名称：">
            <el-input
              clearable
              placeholder="请输入项目名称"
              v-model="fromData.keywords"
              class="form_content_width"
              @keyup.enter.native="userSearchs"
            />
          </el-form-item>
          <el-form-item label="价格类型：">
            <el-select v-model="fromData.price_type" placeholder="请选择" clearable class="form_content_width" @change="userSearchs">
              <el-option label="全部" value="" />
              <el-option label="按人计价" :value="1" />
              <el-option label="按团计价" :value="2" />
            </el-select>
          </el-form-item>
          <el-form-item label="服务商：">
            <el-select v-model="fromData.service_provider_id" placeholder="请选择" clearable class="form_content_width" @change="userSearchs">
              <el-option label="全部" value="" />
              <el-option v-for="item in providerList" :key="item.id" :label="item.name" :value="item.id"></el-option>
            </el-select>
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
      <el-button type="primary" v-db-click @click="add">添加研学项目</el-button>

      <el-table
        :data="projectList"
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
        <el-table-column label="封面图" width="90">
          <template slot-scope="scope">
            <div class="pictrue">
              <img v-lazy="scope.row.image" v-if="scope.row.image" />
            </div>
          </template>
        </el-table-column>
        <el-table-column label="项目名称" prop="title" min-width="160">
          <template slot-scope="scope">
            <span>{{ scope.row.title }}</span>
          </template>
        </el-table-column>
        <el-table-column label="价格类型" min-width="100">
          <template slot-scope="scope">
            <el-tag size="small" :type="scope.row.price_type == 1 ? 'primary' : 'warning'">
              {{ scope.row.price_type == 1 ? '按人计价' : '按团计价' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="价格" prop="price" min-width="90">
          <template slot-scope="scope">
            <span>{{ scope.row.price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="划线价" prop="ot_price" min-width="90">
          <template slot-scope="scope">
            <span>{{ scope.row.ot_price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="讲师" prop="lecturer_name" min-width="160">
          <template slot-scope="scope">
            <span>{{ scope.row.lecturer_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="服务商" prop="provider_name" min-width="140">
          <template slot-scope="scope">
            <span>{{ scope.row.provider_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="关联商品" prop="product_name" min-width="180">
          <template slot-scope="scope">
            <span>{{ scope.row.product_name || '—' }}</span>
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

    <el-dialog :visible.sync="modals" closable :title="isEdit ? '编辑研学项目' : '添加研学项目'" width="660" @close="cancel">
      <el-form label-position="right" size="small" ref="form" :rules="rules" :model="form" label-width="100px">
        <el-form-item label="项目名称：" prop="title">
          <el-input v-model="form.title" class="w-420" placeholder="请输入项目名称"></el-input>
        </el-form-item>
        <el-form-item label="封面图：">
          <div class="acea-row">
            <div class="pictrue" v-if="form.image">
              <img v-lazy="form.image" />
              <i class="el-icon-circle-close btndel" v-db-click @click="form.image = ''"></i>
            </div>
            <div v-else class="upLoad acea-row row-center-wrapper" v-db-click @click="modalPicTap">
              <i class="el-icon-picture-outline" style="font-size: 24px"></i>
            </div>
          </div>
        </el-form-item>
        <el-form-item label="项目介绍：">
          <el-input v-model="form.intro" class="w-420" type="textarea" :rows="4" placeholder="请输入项目介绍"></el-input>
        </el-form-item>
        <el-form-item label="价格类型：" prop="price_type">
          <el-radio-group v-model="form.price_type">
            <el-radio :label="1">按人计价</el-radio>
            <el-radio :label="2">按团计价</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="价格：" prop="price">
          <el-input-number v-model="form.price" :min="0" :precision="2" :max="999999" class="w-420"></el-input-number>
          <span class="ivu-ml-8" style="color: #999">（{{ form.price_type == 1 ? '每人价格' : '整团价格' }}）</span>
        </el-form-item>
        <el-form-item label="划线原价：" prop="ot_price">
          <el-input-number v-model="form.ot_price" :min="0" :precision="2" :max="999999" class="w-420"></el-input-number>
        </el-form-item>
        <el-form-item label="讲师：">
          <el-select v-model="form.lecturer_ids" multiple filterable placeholder="请选择讲师" class="w-420">
            <el-option v-for="item in lecturerList" :key="item.id" :label="item.name" :value="item.id"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="服务商：">
          <el-select v-model="form.service_provider_id" placeholder="请选择服务商" clearable class="w-420">
            <el-option v-for="item in providerList" :key="item.id" :label="item.name" :value="item.id"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="排序：">
          <el-input-number v-model="form.sort" :min="0" :max="999" class="w-420"></el-input-number>
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

    <el-dialog :visible.sync="modalPic" width="950px" title="上传封面图" :close-on-click-modal="false">
      <uploadPictures
        :isChoice="isChoice"
        @getPic="getPic"
        :gridBtn="gridBtn"
        :gridPic="gridPic"
        v-if="modalPic"
      ></uploadPictures>
    </el-dialog>
  </div>
</template>
<script>
import { mapState } from 'vuex';
import uploadPictures from '@/components/uploadPictures';
import {
  studyProjectListApi,
  studyProjectInfoApi,
  studyProjectSaveApi,
  lecturerAllApi,
  serviceProviderAllApi,
} from '@/api/study';
export default {
  name: 'studyProject',
  components: {
    uploadPictures,
  },
  data() {
    return {
      loading: false,
      fromData: {
        page: 1,
        limit: 15,
        keywords: '',
        price_type: '',
        service_provider_id: '',
        status: '',
      },
      projectList: [],
      lecturerList: [],
      providerList: [],
      total: 0,
      modals: false,
      modalPic: false,
      isChoice: '单选',
      gridBtn: {
        xl: 4,
        lg: 8,
        md: 8,
        sm: 8,
        xs: 8,
      },
      gridPic: {
        xl: 6,
        lg: 8,
        md: 8,
        sm: 8,
        xs: 8,
      },
      isEdit: false,
      form: {
        id: 0,
        title: '',
        intro: '',
        image: '',
        price_type: 1,
        price: 0,
        ot_price: 0,
        lecturer_ids: [],
        service_provider_id: '',
        sort: 0,
        status: 1,
      },
      rules: {
        title: [{ required: true, message: '请输入项目名称', trigger: 'blur' }],
        price_type: [{ required: true, message: '请选择价格类型', trigger: 'change' }],
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
    this.getLecturerAll();
    this.getProviderAll();
  },
  methods: {
    // 全部启用的讲师（下拉选择）
    getLecturerAll() {
      lecturerAllApi()
        .then((res) => {
          this.lecturerList = res.data || [];
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // 全部启用的服务商（下拉选择）
    getProviderAll() {
      serviceProviderAllApi()
        .then((res) => {
          this.providerList = res.data || [];
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // 添加
    add() {
      this.modals = true;
      this.isEdit = false;
    },
    // 打开图片上传弹窗
    modalPicTap() {
      this.modalPic = true;
    },
    // 获取单张图片
    getPic(pc) {
      this.form.image = pc.att_dir;
      this.modalPic = false;
    },
    // 编辑
    edit(row) {
      studyProjectInfoApi(row.id)
        .then((res) => {
          let info = res.data.info;
          this.form = {
            id: info.id,
            title: info.title,
            intro: info.intro || '',
            image: info.image || '',
            price_type: info.price_type,
            price: Number(info.price),
            ot_price: Number(info.ot_price),
            lecturer_ids: info.lecturer_ids || [],
            service_provider_id: info.service_provider_id || '',
            sort: Number(info.sort),
            status: info.status,
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
        title: '删除研学项目',
        num: 0,
        url: `study/project/del/${row.id}`,
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
      studyProjectListApi(this.fromData)
        .then(async (res) => {
          let data = res.data;
          this.projectList = data.list;
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
      this.fromData.price_type = '';
      this.fromData.service_provider_id = '';
      this.fromData.status = '';
      this.fromData.page = 1;
      this.getList();
    },
    // 修改状态
    onchangeStatus(row) {
      studyProjectSaveApi(row.id, {
        title: row.title,
        intro: row.intro,
        image: row.image,
        price_type: row.price_type,
        price: row.price,
        ot_price: row.ot_price,
        lecturer_ids: row.lecturer_ids,
        service_provider_id: row.service_provider_id,
        sort: row.sort,
        status: row.status,
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
          studyProjectSaveApi(this.form.id, this.form)
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
        title: '',
        intro: '',
        image: '',
        price_type: 1,
        price: 0,
        ot_price: 0,
        lecturer_ids: [],
        service_provider_id: '',
        sort: 0,
        status: 1,
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
.pictrue {
  width: 60px;
  height: 60px;
  border: 1px solid #e2e2e2;
  border-radius: 4px;
  position: relative;
  overflow: hidden;
  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .btndel {
    position: absolute;
    right: 0;
    top: 0;
    color: #fff;
    background: rgba(0, 0, 0, 0.5);
    cursor: pointer;
    padding: 2px;
    font-size: 14px;
  }
}
.upLoad {
  width: 60px;
  height: 60px;
  border: 1px dashed #c0c4cc;
  border-radius: 4px;
  color: #c0c4cc;
  cursor: pointer;
}
</style>
